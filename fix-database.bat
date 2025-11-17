@echo off
echo === MySportsApp Suite Database Fix ===
echo This script will remove the database volume and recreate the containers.
echo WARNING: This will delete all existing data in the database!
echo ===================================

REM Check if Docker is installed
where docker >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo Docker is not installed. Please install Docker Desktop for Windows.
    echo Visit: https://www.docker.com/products/docker-desktop
    echo After installation, restart your computer and run this script again.
    exit /b 1
)

REM Check if Docker is running
docker info >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo Docker is not running. Please start Docker Desktop and run this script again.
    exit /b 1
)

echo Stopping containers...
docker-compose down

echo Removing database volume...
docker volume rm mysportsapp_php_db_data

echo Recreating containers...
docker-compose up -d

echo ===================================
echo Database fix completed successfully!
echo The containers have been recreated and the database has been reinitialized.
echo You should now be able to log in with the default credentials:
echo   Email: admin@mysportsapp.local
echo   Password: Admin123!
echo ===================================

pause