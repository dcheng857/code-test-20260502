#!/bin/bash

set -e

echo "Starting entrypoint script..."

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY not set. Generating one..."
    # Generate and export it for the current process
    TEMP_KEY=$(php artisan key:generate --show --no-ansi)
    export APP_KEY=$TEMP_KEY
    echo "Generated temporary APP_KEY."
else
    echo "APP_KEY is already set."
fi

# Wait for database to be ready
echo "Waiting for database to be ready..."
max_retries=12
count=0

until php artisan db:monitor || [ $count -eq $max_retries ]; do
    echo "Database not ready yet... waiting (Attempt $((count+1))/$max_retries)"
    sleep 5
    count=$((count+1))
done

# Run migrations
echo "Running migrations..."
php artisan migrate --force || { echo "Migration failed!"; exit 1; }

echo "Starting application..."

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
