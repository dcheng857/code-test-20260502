#!/bin/bash

set -e

# Wait for database to be ready
echo "Waiting for MySQL to be ready..."
sleep 10

# Generate APP_KEY if not set
php artisan key:generate --force

# Run migrations
php artisan migrate --force

echo "Starting application..."

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf