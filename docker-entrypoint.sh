#!/bin/bash
set -e

echo "Starting LearnWay application..."

# Run migrations if DATABASE_URL is set
if [ ! -z "$DATABASE_URL" ]; then
    echo "Running database migrations..."
    php bin/console doctrine:migrations:migrate --no-interaction --env=prod || true
else
    echo "WARNING: DATABASE_URL not set, skipping migrations"
fi

# Start Apache in foreground
echo "Starting Apache..."
exec apache2-foreground
