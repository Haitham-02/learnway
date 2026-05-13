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

RUN echo '<Directory /var/www/html/public>\n    AllowOverride All\n</Directory>' > /etc/apache2/conf-available/symfony.conf && a2enconf symfony

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Remove .env to force Symfony to use only runtime environment variables from Render
RUN rm -f .env .env.local

# Create dummy .env for Symfony's Dotenv check (won't be used if APP_ENV is set in runtime)
RUN touch .env

RUN composer install --no-dev --optimize-autoloader --no-scripts

# Install importmap assets
RUN php bin/console importmap:install --no-interaction --env=prod || true

# Pre-warm the cache during build to avoid runtime permission issues
RUN php bin/console cache:warmup --env=prod || true

# Then set all var permissions to allow Apache to write
RUN chmod -R 777 var

# Copy entrypoint script
COPY docker-entrypoint.sh /var/www/html/
RUN chmod +x /var/www/html/docker-entrypoint.sh

ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/*.conf

EXPOSE 80
