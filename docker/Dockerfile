# PHP 8.3 with Apache.
FROM php:8.3-apache

# Install required system packages and PHP extensions.
RUN apt-get update && apt-get install -y \
    curl git unzip libpq-dev libzip-dev zip libpng-dev \
    && docker-php-ext-install pdo pdo_mysql zip gd

# Install Composer.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache modules required by Symfony.
RUN a2enmod rewrite

# Install Symfony CLI in the container for troubleshooting.
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Symfony working directory.
WORKDIR /var/www/symfony

# Apache listens on port 80 inside the container.
EXPOSE 80
