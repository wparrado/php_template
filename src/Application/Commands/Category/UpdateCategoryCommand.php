<?php

declare(strict_types=1);

namespace Application\Commands\Category;

final readonly class UpdateCategoryCommand
{
    public function __construct(
        public string $categoryId,
        public string $name,
        public ?string $description,
    ) {}
}
