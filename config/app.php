<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Facade;

return [
    'name' => env('APP_NAME', 'PHP Hexagonal Template'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [],

    // Persistence backend: 'memory' | 'eloquent'
    'db_backend' => env('DB_BACKEND', 'memory'),

    // Event publisher: 'sync' | 'outbox'
    'event_publisher' => env('EVENT_PUBLISHER', 'sync'),

    'maintenance' => [
        'driver' => 'file',
    ],

    'providers' => \Illuminate\Support\ServiceProvider::defaultProviders()->merge([
        \Infrastructure\Providers\AppServiceProvider::class,
    ])->toArray(),
];
