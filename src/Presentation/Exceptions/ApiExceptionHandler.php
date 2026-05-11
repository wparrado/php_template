<?php

declare(strict_types=1);

namespace Presentation\Exceptions;

use Domain\Exceptions\CategoryNotFoundException;
use Domain\Exceptions\ItemNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class ApiExceptionHandler
{
    public function register(Exceptions $exceptions): void
    {
        $exceptions->render(function (ItemNotFoundException $e, Request $request): JsonResponse {
            return new JsonResponse([
                'error' => 'Not Found',
                'message' => $e->getMessage(),
            ], 404);
        });

        $exceptions->render(function (CategoryNotFoundException $e, Request $request): JsonResponse {
            return new JsonResponse([
                'error' => 'Not Found',
                'message' => $e->getMessage(),
            ], 404);
        });

        $exceptions->render(function (\Domain\Exceptions\DomainException $e, Request $request): JsonResponse {
            return new JsonResponse([
                'error' => 'Domain Error',
                'message' => $e->getMessage(),
            ], 422);
        });

        $exceptions->render(function (ValidationException $e, Request $request): JsonResponse {
            return new JsonResponse([
                'error' => 'Validation Error',
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        });

        $exceptions->render(function (\Throwable $e, Request $request): ?JsonResponse {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'error' => 'Internal Server Error',
                    'message' => app()->environment('production')
                        ? 'An unexpected error occurred.'
                        : $e->getMessage(),
                ], 500);
            }

            return null;
        });
    }
}
