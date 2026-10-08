FROM php:8.2-apache

# Extensión de MongoDB para PHP (libssl-dev es necesaria para la conexión TLS con Atlas)
RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libssl-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Las dependencias se instalan en /opt/app y no en /var/www/html,
# porque ese directorio se sustituye por el volumen del proyecto en docker-compose.yml
WORKDIR /opt/app
COPY composer.json ./
RUN composer install --no-dev --no-interaction --prefer-dist

WORKDIR /var/www/html
