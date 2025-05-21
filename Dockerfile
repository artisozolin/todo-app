FROM php:8.0-fpm

RUN apt-get update && apt-get install -y \
    default-libmysqlclient-dev \
    unzip \
    zip \
    curl \
    git \
    && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
