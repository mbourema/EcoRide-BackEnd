FROM php:8.2-fpm

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev zip libxml2-dev \
    libonig-dev libcurl4-openssl-dev gnupg curl libssl-dev \
    default-mysql-client libpng-dev pkg-config libssl-dev \
    && docker-php-ext-install pdo pdo_mysql intl zip opcache

# Installation de l'extension MongoDB
RUN pecl install mongodb-1.20.0 \
    && docker-php-ext-enable mongodb

# Installation de Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/symfony
COPY . .

# Installation des dépendances PHP
RUN composer install --no-scripts --no-interaction

# Permissions
RUN chown -R www-data:www-data /var/www/symfony

EXPOSE 9000
CMD ["php-fpm"]

