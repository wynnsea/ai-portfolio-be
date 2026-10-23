#!/bin/sh
set -e

# Only the main app container prepares the database, so the queue worker
# does not run migrations at the same time.
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
    # Seeders only fill empty tables, so this is safe on every start.
    php artisan db:seed --force
fi

php artisan optimize

exec "$@"
