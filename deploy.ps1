# MySportsApp Suite - Deployment Script for Windows
# This script deploys the application directly from a GitHub URL

param(
    [string]$GitHubUrl = "https://github.com/yourusername/MySportsApp-Suite.git",
    [string]$Branch = "main"
)

Write-Host "=== MySportsApp Suite Deployment ===" -ForegroundColor Cyan
Write-Host "Deploying from: $GitHubUrl (branch: $Branch)" -ForegroundColor Cyan
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

# Create app directory if it doesn't exist
$AppDir = "C:\MySportsApp-Suite"
if (-not (Test-Path $AppDir)) {
    New-Item -ItemType Directory -Path $AppDir | Out-Null
    Write-Host "Created directory: $AppDir" -ForegroundColor Green
}
Set-Location $AppDir

# Remove existing files if this is a redeployment
if (Test-Path ".git") {
    Write-Host "Existing repository found. Removing..." -ForegroundColor Yellow
    Remove-Item -Recurse -Force * -ErrorAction SilentlyContinue
    Remove-Item -Recurse -Force .git -ErrorAction SilentlyContinue
}

# Clone the repository
Write-Host "Cloning repository from $GitHubUrl..." -ForegroundColor Green
git clone $GitHubUrl -b $Branch .

# Build and start the Docker containers
Write-Host "Building and starting Docker containers..." -ForegroundColor Green
docker-compose build
docker-compose up -d

# Get the server's IP address (localhost for local development)
$ServerIP = "localhost"

Write-Host "===================================" -ForegroundColor Cyan
Write-Host "Deployment completed successfully!" -ForegroundColor Green
Write-Host "You can access the application at: http://$ServerIP" -ForegroundColor Green
Write-Host "Default login:" -ForegroundColor Green
Write-Host "  Email: admin@mysportsapp.local" -ForegroundColor Green
Write-Host "  Password: Admin123!" -ForegroundColor Green
Write-Host "===================================" -ForegroundColor Cyan