#!/bin/bash
# Don't exit immediately on error, as we want PHP-FPM to start regardless
set +e

# Run the database initialization script
echo "Running database initialization script..."
/var/www/html/docker/init-db.sh
INIT_DB_STATUS=$?

if [ $INIT_DB_STATUS -ne 0 ]; then
    echo "WARNING: Database initialization script exited with status $INIT_DB_STATUS"
    echo "Continuing anyway to ensure PHP-FPM starts"
    echo "The healthcheck script will retry database operations later"
fi

# Start PHP-FPM
echo "Starting PHP-FPM..."
exec php-fpm