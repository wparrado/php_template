<?php

declare(strict_types=1);

namespace Domain\Ports\Outbound;

use Domain\Model\Example\Category;

interface CategoryRepositoryInterface
{
    public function save(Category $category): void;

    public function findById(string $id): ?Category;

    /**
     * @return Category[]
     */
    public function findAll(int $limit = 20, int $offset = 0): array;

    public function delete(string $id): void;

    public function count(): int;
}
