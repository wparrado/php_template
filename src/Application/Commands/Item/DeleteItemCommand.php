<?php

declare(strict_types=1);

namespace Application\Commands\Item;

final readonly class DeleteItemCommand
{
    public function __construct(
        public string $itemId,
    ) {}
}
