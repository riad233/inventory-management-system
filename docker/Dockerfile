# Multi-stage Dockerfile for IMS application
# Stage 1: Builder - Optimize dependencies and assets
FROM php:8.2-apache AS builder

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    zip \
    unzip \
    curl \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    mysqli \
    pdo \
    pdo_mysql \
    zip \
    && docker-php-ext-enable mysqli pdo pdo_mysql zip

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application code
COPY . /app/

# Install PHP dependencies if composer.json exists
RUN if [ -f "composer.json" ]; then composer install --no-dev --optimize-autoloader; fi

# Stage 2: Runtime - Lean production image
FROM php:8.2-apache

# Install minimal runtime dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    curl \
    mariadb-client \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    mysqli \
    pdo \
    pdo_mysql \
    zip \
    && docker-php-ext-enable mysqli pdo pdo_mysql zip

# Copy Apache configuration
COPY docker/apache.conf /etc/apache2/apache2.conf
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Enable Apache modules
RUN a2enmod rewrite headers expires

# Set working directory
WORKDIR /var/www/html

# Copy application from builder
COPY --from=builder /app /var/www/html

# Create necessary directories
RUN mkdir -p storage/logs storage/cache public/uploads && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 775 storage public/uploads

# Set environment variables
ENV APACHE_RUN_USER=www-data \
    APACHE_RUN_GROUP=www-data \
    APACHE_PID_FILE=/var/run/apache2.pid \
    APACHE_RUN_DIR=/var/run/apache2 \
    APACHE_LOCK_DIR=/var/lock/apache2 \
    APACHE_LOG_DIR=/var/log/apache2

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/api/health/status || exit 1

# Start Apache in foreground
CMD ["apache2-foreground"]
