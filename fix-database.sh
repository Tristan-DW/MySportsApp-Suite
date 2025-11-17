#!/bin/bash

# MySportsApp Suite - Database Fix Script
# This script fixes database initialization issues by removing the database volume and recreating the containers

# Exit on error
set -e

echo "=== MySportsApp Suite Database Fix ==="
echo "This script will remove the database volume and recreate the containers."
echo "WARNING: This will delete all existing data in the database!"
echo "==================================="

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "Docker is not installed. Please install Docker first."
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Stop the containers
echo "Stopping containers..."
docker-compose down

# Remove the database volume
echo "Removing database volume..."
docker volume rm mysportsapp_php_db_data

# Recreate the containers
echo "Recreating containers..."
docker-compose up -d

echo "==================================="
echo "Database fix completed successfully!"
echo "The containers have been recreated and the database has been reinitialized."
echo "You should now be able to log in with the default credentials:"
echo "  Email: admin@mysportsapp.local"
echo "  Password: Admin123!"
echo "==================================="