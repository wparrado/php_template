<?php

declare(strict_types=1);

namespace Application\Queries\Item;

final readonly class ListItemsQuery
{
    public function __construct(
        public int $limit = 20,
        public int $offset = 0,
    ) {}
}
