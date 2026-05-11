<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\InMemory;

use Domain\Model\Example\Category;
use Domain\Ports\Outbound\CategoryRepositoryInterface;

final class InMemoryCategoryRepository implements CategoryRepositoryInterface
{
    /** @var array<string, Category> */
    private array $categories = [];

    public function save(Category $category): void
    {
        $this->categories[$category->getId()->toString()] = $category;
    }

    public function findById(string $id): ?Category
    {
        return $this->categories[$id] ?? null;
    }

    /**
     * @return Category[]
     */
    public function findAll(int $limit = 20, int $offset = 0): array
    {
        $all = array_values($this->categories);

        usort($all, fn (Category $a, Category $b) => $b->getCreatedAt() <=> $a->getCreatedAt());

        return array_slice($all, $offset, $limit);
    }

    public function delete(string $id): void
    {
        unset($this->categories[$id]);
    }

    public function count(): int
    {
        return count($this->categories);
    }

    /** Reset state between tests. */
    public function clear(): void
    {
        $this->categories = [];
    }
}
