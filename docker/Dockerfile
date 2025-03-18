# Usamos PHP 8.3 con Apache
FROM php:8.3-apache

# Instalamos dependencias del sistema
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev zip libpng-dev \
    && docker-php-ext-install pdo pdo_mysql zip gd

# Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilitamos módulos de Apache
RUN a2enmod rewrite

# Instalar Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Configuración de Symfony: creamos un directorio de trabajo
WORKDIR /var/www/symfony

# Exponer el puerto 8000
EXPOSE 8000
