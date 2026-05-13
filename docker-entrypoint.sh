#!/bin/bash
set -e

echo "Starting LearnWay application..."

# Run migrations if DATABASE_URL is set
if [ ! -z "$DATABASE_URL" ]; then
    echo "Running database migrations..."
    php bin/console doctrine:migrations:migrate --no-interaction --env=prod || true
fi

# Install importmap assets if needed
echo "Ensuring importmap assets are installed..."
php bin/console importmap:install --no-interaction --env=prod 2>&1 || true

# Start Apache in foreground
echo "Starting Apache..."
exec apache2-foreground
