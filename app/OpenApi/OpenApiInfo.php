<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'PHP Template API',
    version: '1.0.0',
    description: 'Hexagonal Laravel template API',
    contact: new OA\Contact(name: 'Maintainer', email: 'dev@example.com')
)]
class OpenApiInfo {}
