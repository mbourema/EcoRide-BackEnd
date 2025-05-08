# PHP avec extensions requises
FROM php:8.2-fpm-alpine

# Installation des dépendances système et extensions PHP
RUN apk add --no-cache \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libxml2-dev \
    zlib-dev \
    oniguruma-dev \
    curl-dev \
    gmp-dev \
    postgresql-dev \
    mysql-client \
    git \
    unzip \
    autoconf \
    make \
    g++ && \
    pecl install mongodb-1.20.0 && \
    docker-php-ext-enable mongodb && \
    docker-php-ext-configure gd --with-jpeg --with-webp && \
    docker-php-ext-install gd zip pdo pdo_mysql pdo_pgsql xml curl gmp zip

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copie du code source de l'application
WORKDIR /var/www
COPY . /var/www/

# Supprime MakerBundle avant l'installation des dépendances
RUN rm -rf var/cache/* && \
    composer install --no-dev --optimize-autoloader --no-scripts && \
    composer run-script post-install-cmd --no-dev && \
    chown -R www-data:www-data /var/cache /var/log

# Variables d'environnement
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Exposer le port FPM
EXPOSE 9000
CMD ["php-fpm"]
