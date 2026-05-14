<?php

declare(strict_types=1);

namespace Application\DTOs;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Category',
    type: 'object',
    required: ['id','name'],
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
    ]
)]
final readonly class CategoryOutputDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description,
        public string $createdAt,
        public string $updatedAt,
    ) {}
}
