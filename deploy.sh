#!/bin/bash

# MySportsApp Suite - Deployment Script for Hostinger VPS
# This script deploys the application directly from a GitHub URL

# Exit on error
set -e

# Default GitHub URL (can be overridden with command line argument)
GITHUB_URL=${1:-"https://github.com/yourusername/MySportsApp-Suite.git"}
BRANCH=${2:-"main"}

echo "=== MySportsApp Suite Deployment ==="
echo "Deploying from: $GITHUB_URL (branch: $BRANCH)"
echo "==================================="

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "Docker is not installed. Installing Docker..."
    
    # Update package lists
    apt-get update
    
    # Install prerequisites
    apt-get install -y apt-transport-https ca-certificates curl software-properties-common
    
    # Add Docker's official GPG key
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | apt-key add -
    
    # Add Docker repository (assuming Ubuntu - adjust if using a different distro)
    add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable"
    
    # Update package lists again
    apt-get update
    
    # Install Docker CE
    apt-get install -y docker-ce
    
    echo "Docker installed successfully!"
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "Docker Compose is not installed. Installing Docker Compose..."
    
    # Install Docker Compose
    curl -L "https://github.com/docker/compose/releases/download/v2.20.3/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
    
    echo "Docker Compose installed successfully!"
fi

# Create app directory if it doesn't exist
APP_DIR="/opt/mysportsapp-suite"
mkdir -p $APP_DIR
cd $APP_DIR

# Remove existing files if this is a redeployment
if [ -d ".git" ]; then
    echo "Existing repository found. Removing..."
    rm -rf *
    rm -rf .git
fi

# Clone the repository
echo "Cloning repository from $GITHUB_URL..."
git clone $GITHUB_URL -b $BRANCH .

# Build and start the Docker containers
echo "Building and starting Docker containers..."
docker-compose build
docker-compose up -d

# Get the server's public IP
SERVER_IP=$(curl -s ifconfig.me)

echo "==================================="
echo "Deployment completed successfully!"
echo "You can access the application at: http://$SERVER_IP"
echo "Default login:"
echo "  Email: admin@mysportsapp.local"
echo "  Password: Admin123!"
echo "==================================="