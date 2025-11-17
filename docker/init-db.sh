#!/bin/bash
# Don't exit immediately on error, as we want to provide diagnostics
set +e

# Function to log messages with timestamp
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

log_message "Starting database initialization script"
log_message "Environment variables: MYSQL_DATABASE=${MYSQL_DATABASE:-not set}"
log_message "Environment variables: MYSQL_USER=${MYSQL_USER:-not set}"
log_message "Environment variables: MYSQL_PASSWORD=${MYSQL_PASSWORD:+is set}"

# Check if schema.sql exists
if [ ! -f /var/www/html/database/schema.sql ]; then
    log_message "ERROR: schema.sql file not found at /var/www/html/database/schema.sql"
    ls -la /var/www/html/database/
    exit 1
fi

# Initialize flag to indicate if we should skip MySQL operations
SKIP_MYSQL_OPERATIONS=0

# Wait for MySQL to be ready
log_message "Waiting for MySQL to be ready..."
max_attempts=60  # Increased to 60 (300 seconds total)
attempt=0
while true; do
    attempt=$((attempt+1))
    
    # Try to connect to MySQL and capture any error message
    connection_result=$(mysql -h db -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "SELECT 1" 2>&1)
    connection_status=$?
    
    if [ $connection_status -eq 0 ]; then
        log_message "MySQL is ready!"
        break
    fi
    
    # Log the specific error for better diagnostics
    log_message "MySQL not ready yet. Error: $connection_result"
    
    if [ $attempt -gt $max_attempts ]; then
        log_message "ERROR: MySQL did not become ready in time after $max_attempts attempts"
        log_message "Last error: $connection_result"
        
        # Try to get more information about the MySQL server
        log_message "Attempting to diagnose the issue..."
        
        # Check if we can ping the db container
        if ping -c 1 db >/dev/null 2>&1; then
            log_message "Can ping the db container"
        else
            log_message "Cannot ping the db container"
        fi
        
        # Check if the port is open
        if nc -z db 3306 >/dev/null 2>&1; then
            log_message "MySQL port is open on db host"
        else
            log_message "MySQL port is NOT open on db host"
        fi
        
        # Try connecting with different parameters
        log_message "Trying to connect to MySQL with different parameters..."
        mysql -h 127.0.0.1 -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "SELECT 1" >/dev/null 2>&1
        if [ $? -eq 0 ]; then
            log_message "Can connect to MySQL using 127.0.0.1 instead of 'db'"
        else
            log_message "Cannot connect to MySQL using 127.0.0.1"
        fi
        
        # Continue anyway, as we want PHP-FPM to start
        log_message "Will continue despite connection issues. PHP-FPM will start, and the healthcheck script will retry later."
        log_message "Setting a flag to indicate that we should skip further MySQL operations"
        SKIP_MYSQL_OPERATIONS=1
        break
    fi
    
    log_message "MySQL not ready yet. Waiting... (Attempt $attempt/$max_attempts)"
    sleep 5  # Increased from 2 to 5 seconds
done

# Only proceed with MySQL operations if we didn't skip them
if [ $SKIP_MYSQL_OPERATIONS -eq 0 ]; then
    # Check if the database exists
    log_message "Checking if database exists..."
    if ! mysql -h db -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "USE $MYSQL_DATABASE" >/dev/null 2>&1; then
        log_message "Database does not exist. Creating database $MYSQL_DATABASE..."
        mysql -h db -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS $MYSQL_DATABASE"
        log_message "Database created successfully!"
    fi

    # Check if the users table exists
    log_message "Checking if database schema is initialized..."
    if ! mysql -h db -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "DESCRIBE users" >/dev/null 2>&1; then
        log_message "Database schema not initialized. Applying schema.sql..."
        
        # Apply schema with error handling
        if mysql -h db -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" < /var/www/html/database/schema.sql; then
            log_message "Schema applied successfully!"
        else
            log_message "ERROR: Failed to apply schema.sql"
            # Don't exit, let the healthcheck handle it
            log_message "Continuing despite schema application failure"
        fi
    else
        log_message "Database schema already initialized."
    fi

    # Verify all required tables exist
    log_message "Verifying all required tables exist..."
    required_tables=("users" "analytics_sources" "knowledge_articles" "tickets" "ticket_notes" "settings" "paystack_settlements" "paystack_transactions" "xero_connections")
    missing_tables=()

    for table in "${required_tables[@]}"; do
        log_message "Checking table: $table"
        if ! mysql -h db -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "DESCRIBE $table" >/dev/null 2>&1; then
            missing_tables+=("$table")
            log_message "Table $table is missing"
        fi
    done

    if [ ${#missing_tables[@]} -gt 0 ]; then
        log_message "Warning: The following tables are missing: ${missing_tables[*]}"
        log_message "Applying schema.sql to create missing tables..."
        
        # Apply schema with error handling
        if mysql -h db -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" < /var/www/html/database/schema.sql; then
            log_message "Schema applied successfully!"
            
            # Verify tables were created
            still_missing=()
            for table in "${missing_tables[@]}"; do
                if ! mysql -h db -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "DESCRIBE $table" >/dev/null 2>&1; then
                    still_missing+=("$table")
                fi
            done
            
            if [ ${#still_missing[@]} -gt 0 ]; then
                log_message "ERROR: The following tables are still missing after applying schema.sql: ${still_missing[*]}"
                log_message "This may indicate an issue with the schema.sql file"
                # Continue anyway, as the health check will catch this
            fi
        else
            log_message "ERROR: Failed to apply schema.sql"
            # Don't exit, let the healthcheck handle it
            log_message "Continuing despite schema application failure"
        fi
    fi

    # Verify users table has at least one record
    log_message "Verifying users table has data..."
    user_count=$(mysql -h db -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" -N -e "SELECT COUNT(*) FROM $MYSQL_DATABASE.users" 2>/dev/null || echo "0")
    log_message "Users table has $user_count records"

    if [ "$user_count" -lt 1 ]; then
        log_message "Warning: Users table is empty. Applying schema.sql to create default user..."
        mysql -h db -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" < /var/www/html/database/schema.sql
        
        # Verify user was created
        user_count=$(mysql -h db -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" -N -e "SELECT COUNT(*) FROM $MYSQL_DATABASE.users" 2>/dev/null || echo "0")
        if [ "$user_count" -lt 1 ]; then
            log_message "ERROR: Failed to create default user"
            # Continue anyway, as the health check will catch this
        else
            log_message "Default user created successfully!"
        fi
    fi
else
    log_message "Skipping MySQL operations due to connection issues"
    log_message "The healthcheck script will retry database operations later"
fi

log_message "Database initialization complete!"