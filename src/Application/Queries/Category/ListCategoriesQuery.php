<?php

declare(strict_types=1);

namespace Application\Queries\Category;

final readonly class ListCategoriesQuery
{
    public function __construct(
        public int $limit = 20,
        public int $offset = 0,
    ) {}
}
