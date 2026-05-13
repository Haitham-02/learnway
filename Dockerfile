FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libzip-dev libonig-dev \
    libxml2-dev libjpeg62-turbo-dev libpng-dev libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
    pdo pdo_mysql intl zip mbstring gd

RUN docker-php-ext-install -j1 dom xml

RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY .env .env

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-scripts

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/*.conf

EXPOSE 80
