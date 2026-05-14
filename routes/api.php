<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Presentation\Http\Controllers\Api\V1\CategoryController;
use Presentation\Http\Controllers\Api\V1\ItemController;

Route::prefix('v1')->group(function (): void {
    Route::apiResource('items', ItemController::class);
    Route::apiResource('categories', CategoryController::class);
});

Route::get('/health', fn () => response()->json(['status' => 'ok', 'timestamp' => now()->toISOString()]));

// Swagger UI and JSON endpoints (served under /api/...)
Route::get('/documentation', function () {
    // Use relative URL to avoid cross-origin issues when serving UI from different hosts (localhost vs 127.0.0.1)
    $url = '/api/docs.json';
    return view('swagger-ui', ['url' => $url]);
});

Route::get('/docs.json', function () {
    $path = storage_path('api-docs/api-docs.json');

    if (! file_exists($path)) {
        abort(404, 'OpenAPI JSON not found. Run php artisan openapi:generate');
    }

    return response()->file($path, ['Content-Type' => 'application/json']);
});
