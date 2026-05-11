<?php

declare(strict_types=1);

namespace Application\DTOs;

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
