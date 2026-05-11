<?php

declare(strict_types=1);

namespace Application\Commands\Item;

final readonly class UpdateItemCommand
{
    public function __construct(
        public string $itemId,
        public string $name,
        public float $price,
        public string $currency,
        public ?string $description,
        public ?string $categoryId,
    ) {}
}
