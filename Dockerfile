FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libzip-dev libonig-dev \
    libxml2-dev libjpeg62-turbo-dev libpng-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
    pdo pdo_mysql intl zip mbstring gd \
    && docker-php-ext-install -j1 dom xml \
    && docker-php-ext-install -j1 xmlreader xmlwriter simplexml \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-scripts

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/*.conf

EXPOSE 80
