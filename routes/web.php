<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/api/documentation');
});

// Swagger UI (lightweight, uses swagger-ui CDN)
Route::get('/api/documentation', function () {
    $url = url('/api/docs.json');

    return view('swagger-ui', ['url' => $url]);
});

// Serve generated OpenAPI JSON (generated with php artisan openapi:generate)
Route::get('/api/docs.json', function () {
    $path = storage_path('api-docs/api-docs.json');

    if (! file_exists($path)) {
        abort(404, 'OpenAPI JSON not found. Run php artisan openapi:generate');
    }

    return response()->file($path, ['Content-Type' => 'application/json']);
});
