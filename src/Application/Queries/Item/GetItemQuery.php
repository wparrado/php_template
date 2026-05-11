<?php

declare(strict_types=1);

namespace Application\Queries\Item;

final readonly class GetItemQuery
{
    public function __construct(
        public string $itemId,
    ) {}
}
