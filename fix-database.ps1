# MySportsApp Suite - Database Fix Script for Windows
# This script fixes database initialization issues by removing the database volume and recreating the containers

Write-Host "=== MySportsApp Suite Database Fix ===" -ForegroundColor Cyan
Write-Host "This script will remove the database volume and recreate the containers." -ForegroundColor Cyan
Write-Host "WARNING: This will delete all existing data in the database!" -ForegroundColor Red
Write-Host "===================================" -ForegroundColor Cyan

# Check if Docker is installed
if (-not (Get-Command "docker" -ErrorAction SilentlyContinue)) {
    Write-Host "Docker is not installed. Please install Docker Desktop for Windows from:" -ForegroundColor Yellow
    Write-Host "https://www.docker.com/products/docker-desktop" -ForegroundColor Yellow
    Write-Host "After installation, restart your computer and run this script again." -ForegroundColor Yellow
    exit 1
}

# Check if Docker is running
try {
    $null = docker info
    Write-Host "Docker is running." -ForegroundColor Green
} catch {
    Write-Host "Docker is not running. Please start Docker Desktop and run this script again." -ForegroundColor Red
    exit 1
}

# Stop the containers
Write-Host "Stopping containers..." -ForegroundColor Green
docker-compose down

# Remove the database volume
Write-Host "Removing database volume..." -ForegroundColor Green
docker volume rm mysportsapp_php_db_data

# Recreate the containers
Write-Host "Recreating containers..." -ForegroundColor Green
docker-compose up -d

Write-Host "===================================" -ForegroundColor Cyan
Write-Host "Database fix completed successfully!" -ForegroundColor Green
Write-Host "The containers have been recreated and the database has been reinitialized." -ForegroundColor Green
Write-Host "You should now be able to log in with the default credentials:" -ForegroundColor Green
Write-Host "  Email: admin@mysportsapp.local" -ForegroundColor Green
Write-Host "  Password: Admin123!" -ForegroundColor Green
Write-Host "===================================" -ForegroundColor Cyan