FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    bash \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    curl \
 && docker-php-ext-install pdo pdo_mysql intl mbstring zip

WORKDIR /var/www/html

# COPY FULL REPO INTO IMAGE
COPY . /var/www/html
