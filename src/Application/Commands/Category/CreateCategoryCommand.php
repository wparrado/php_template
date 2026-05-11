<?php

declare(strict_types=1);

namespace Application\Commands\Category;

final readonly class CreateCategoryCommand
{
    public function __construct(
        public string $name,
        public ?string $description,
    ) {}
}
