FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    bash \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    curl \
    mysql-client \
 && docker-php-ext-install pdo pdo_mysql intl mbstring zip

WORKDIR /var/www/html

# COPY FULL REPO INTO IMAGE
COPY . /var/www/html

# Make scripts executable
RUN chmod +x /var/www/html/docker/init-db.sh /var/www/html/docker/entrypoint.sh

# Set entrypoint
ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
