<?php

declare(strict_types=1);

namespace Application\Commands\Category;

final readonly class DeleteCategoryCommand
{
    public function __construct(
        public string $categoryId,
    ) {}
}
