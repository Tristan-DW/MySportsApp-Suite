#!/bin/bash
set -e

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
max_attempts=30
attempt=0
while ! mysql -h db -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "SELECT 1" >/dev/null 2>&1; do
    attempt=$((attempt+1))
    if [ $attempt -gt $max_attempts ]; then
        echo "Error: MySQL did not become ready in time"
        exit 1
    fi
    echo "MySQL not ready yet. Waiting..."
    sleep 2
done
echo "MySQL is ready!"

# Check if the users table exists
echo "Checking if database schema is initialized..."
if ! mysql -h db -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "DESCRIBE users" >/dev/null 2>&1; then
    echo "Database schema not initialized. Applying schema.sql..."
    mysql -h db -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" < /var/www/html/database/schema.sql
    echo "Schema applied successfully!"
else
    echo "Database schema already initialized."
fi

# Verify all required tables exist
required_tables=("users" "analytics_sources" "knowledge_articles" "tickets" "ticket_notes" "settings" "paystack_settlements" "paystack_transactions" "xero_connections")
missing_tables=()

for table in "${required_tables[@]}"; do
    if ! mysql -h db -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "DESCRIBE $table" >/dev/null 2>&1; then
        missing_tables+=("$table")
    fi
done

if [ ${#missing_tables[@]} -gt 0 ]; then
    echo "Warning: The following tables are missing: ${missing_tables[*]}"
    echo "Applying schema.sql to create missing tables..."
    mysql -h db -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" < /var/www/html/database/schema.sql
    echo "Schema applied successfully!"
fi

echo "Database initialization complete!"