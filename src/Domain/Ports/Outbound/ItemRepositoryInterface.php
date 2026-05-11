<?php

declare(strict_types=1);

namespace Domain\Ports\Outbound;

use Domain\Model\Example\Item;

interface ItemRepositoryInterface
{
    public function save(Item $item): void;

    public function findById(string $id): ?Item;

    /**
     * @return Item[]
     */
    public function findAll(int $limit = 20, int $offset = 0): array;

    public function delete(string $id): void;

    public function count(): int;
}
