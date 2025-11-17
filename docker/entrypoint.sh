#!/bin/bash
set -e

# Run the database initialization script
echo "Running database initialization script..."
/var/www/html/docker/init-db.sh

# Start PHP-FPM
echo "Starting PHP-FPM..."
exec php-fpm