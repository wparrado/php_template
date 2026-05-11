<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\InMemory;

use Domain\Model\Example\Item;
use Domain\Ports\Outbound\ItemRepositoryInterface;

final class InMemoryItemRepository implements ItemRepositoryInterface
{
    /** @var array<string, Item> */
    private array $items = [];

    public function save(Item $item): void
    {
        $this->items[$item->getId()->toString()] = $item;
    }

    public function findById(string $id): ?Item
    {
        return $this->items[$id] ?? null;
    }

    /**
     * @return Item[]
     */
    public function findAll(int $limit = 20, int $offset = 0): array
    {
        $active = array_filter(
            array_values($this->items),
            fn (Item $item) => !$item->isDeleted()
        );

        // Sort by createdAt desc
        usort($active, fn (Item $a, Item $b) => $b->getCreatedAt() <=> $a->getCreatedAt());

        return array_slice($active, $offset, $limit);
    }

    public function delete(string $id): void
    {
        unset($this->items[$id]);
    }

    public function count(): int
    {
        return count(array_filter(
            $this->items,
            fn (Item $item) => !$item->isDeleted()
        ));
    }

    /** Reset state between tests. */
    public function clear(): void
    {
        $this->items = [];
    }
}
