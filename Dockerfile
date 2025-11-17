FROM php:8.2-fpm-alpine

# Install PHP extensions we need
RUN apk add --no-cache \
    bash \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    curl \
 && docker-php-ext-install pdo pdo_mysql intl mbstring zip

WORKDIR /var/www/html

# Copy the full repo into the image
COPY . /var/www/html

# You can add custom php.ini etc here if needed
