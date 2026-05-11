<?php

declare(strict_types=1);

namespace Application\Queries\Category;

final readonly class GetCategoryQuery
{
    public function __construct(
        public string $categoryId,
    ) {}
}
