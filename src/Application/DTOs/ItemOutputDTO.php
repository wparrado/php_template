<?php

declare(strict_types=1);

namespace Application\DTOs;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Item',
    type: 'object',
    required: ['id','name'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', format: 'int64'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'price', type: 'number', format: 'float'),
        new OA\Property(property: 'currency', type: 'string'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'categoryId', type: 'string', nullable: true),
        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
    ]
)]
final readonly class ItemOutputDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public float $price,
        public string $currency,
        public ?string $description,
        public ?string $categoryId,
        public string $createdAt,
        public string $updatedAt,
    ) {}
}
