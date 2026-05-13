FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libzip-dev libonig-dev \
    libxml2-dev libjpeg62-turbo-dev libpng-dev libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
    pdo pdo_mysql intl zip mbstring gd

RUN docker-php-ext-install -j1 dom xml

ENV APP_ENV=prod
ENV APP_DEBUG=0

RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Remove .env to force Symfony to use only runtime environment variables from Render
RUN rm -f .env .env.local

# Create dummy .env for Symfony's Dotenv check (won't be used if APP_ENV is set in runtime)
RUN touch .env

RUN composer install --no-dev --optimize-autoloader --no-scripts

# Remove and recreate var directory with full permissions for Apache
RUN rm -rf var && mkdir -p var/cache var/log && chmod -R 777 var

RUN php bin/console cache:clear --env=prod || true
RUN php bin/console doctrine:migrations:migrate --no-interaction --env=prod || true

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/*.conf

EXPOSE 80
