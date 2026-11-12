#!/bin/sh
# Builds Laravel caches from the runtime environment, then runs the command
# (php-fpm, queue worker or scheduler). Migrations run in the deploy step.
set -e

if [ -z "$APP_KEY" ]; then
    echo "APP_KEY is not set. See docs/operations/docker-deploy.md." >&2
    exit 1
fi

php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction
php artisan event:cache --no-interaction

exec "$@"
