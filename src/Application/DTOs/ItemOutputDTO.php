<?php

declare(strict_types=1);

namespace Application\DTOs;

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
