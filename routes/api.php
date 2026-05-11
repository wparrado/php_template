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
