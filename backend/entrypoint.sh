#!/bin/bash

# Wait for Redis
echo "Waiting for Redis..."
MAX_WAIT=10
WAIT_COUNT=0

while ! nc -z redis 6379; do
    echo "Redis is unavailable - sleeping"
    sleep 2
    
    WAIT_COUNT=$((WAIT_COUNT + 1))
    if [ $WAIT_COUNT -ge $MAX_WAIT ]; then
        echo "Redis is still unavailable after 10 seconds. Continuing anyway..."
        break
    fi
done

if [ $WAIT_COUNT -lt $MAX_WAIT ]; then
    echo "Redis is ready!"
fi

# Wait for MySQL/PostgreSQL
echo "Waiting for database..."
while ! nc -z database 3306; do
    echo "Database is unavailable - sleeping"
    sleep 2
done
echo "Database is ready!"

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Run seeders (idempotent)
echo "Running seeders..."
php artisan db:seed --force

# Start the application
echo "Starting application..."
exec "$@"