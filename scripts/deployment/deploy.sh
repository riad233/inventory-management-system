#!/bin/bash

# IMS Deployment Script
# Orchestrates the full deployment process
# Usage: ./scripts/deployment/deploy.sh [environment] [image_tag]

set -euo pipefail

# Configuration
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$(dirname "$SCRIPT_DIR")")"
ENVIRONMENT="${1:-production}"
IMAGE_TAG="${2:-latest}"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Logging functions
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Exit with error
fail() {
    log_error "$1"
    exit 1
}

# Deployment steps
main() {
    log_info "Starting deployment for $ENVIRONMENT environment"
    log_info "Image tag: $IMAGE_TAG"
    echo

    # Step 1: Pre-deployment checks
    log_info "Step 1: Pre-deployment checks..."
    pre_deployment_checks

    # Step 2: Pull latest code
    log_info "Step 2: Pulling latest code..."
    pull_code

    # Step 3: Build Docker image
    log_info "Step 3: Building Docker image..."
    build_image

    # Step 4: Stop existing containers
    log_info "Step 4: Stopping existing containers..."
    stop_containers

    # Step 5: Start new containers
    log_info "Step 5: Starting new containers..."
    start_containers

    # Step 6: Run migrations
    log_info "Step 6: Running database migrations..."
    run_migrations

    # Step 7: Clear caches
    log_info "Step 7: Clearing application caches..."
    clear_caches

    # Step 8: Health check
    log_info "Step 8: Running health checks..."
    health_check

    # Step 9: Post-deployment tasks
    log_info "Step 9: Post-deployment tasks..."
    post_deployment

    log_info "Deployment completed successfully!"
    echo
}

pre_deployment_checks() {
    # Check Docker is running
    if ! command -v docker &> /dev/null; then
        fail "Docker is not installed"
    fi

    # Check docker-compose
    if ! command -v docker-compose &> /dev/null; then
        fail "Docker Compose is not installed"
    fi

    # Check PHP is available
    if ! command -v php &> /dev/null; then
        fail "PHP is not installed"
    fi

    log_info "All prerequisites met"
}

pull_code() {
    cd "$PROJECT_ROOT"
    
    if git rev-parse --git-dir > /dev/null 2>&1; then
        git fetch origin
        git checkout main
        git pull origin main
        log_info "Code pulled successfully"
    else
        log_warn "Not a git repository, skipping pull"
    fi
}

build_image() {
    cd "$PROJECT_ROOT"
    
    docker build \
        -f docker/Dockerfile \
        -t "ims:$IMAGE_TAG" \
        .
    
    log_info "Docker image built: ims:$IMAGE_TAG"
}

stop_containers() {
    cd "$PROJECT_ROOT"
    
    docker-compose down || true
    
    log_info "Containers stopped"
}

start_containers() {
    cd "$PROJECT_ROOT"
    
    docker-compose up -d
    
    # Wait for services to be ready
    log_info "Waiting for services to be ready..."
    sleep 10
    
    log_info "Containers started"
}

run_migrations() {
    cd "$PROJECT_ROOT"
    
    docker-compose exec -T app php scripts/deployment/migrate.php || fail "Migrations failed"
    
    log_info "Migrations completed"
}

clear_caches() {
    cd "$PROJECT_ROOT"
    
    docker-compose exec -T app php scripts/deployment/cache-clear.php || fail "Cache clear failed"
    
    log_info "Caches cleared"
}

health_check() {
    cd "$PROJECT_ROOT"
    
    if docker-compose exec -T app php scripts/deployment/health-check.php; then
        log_info "Health checks passed"
    else
        fail "Health checks failed"
    fi
}

post_deployment() {
    log_info "Sending deployment notification..."
    # Add post-deployment tasks here (notifications, monitoring, etc.)
}

# Run main function
main "$@"
