<?php

declare(strict_types=1);

namespace Application\DTOs;

final readonly class PaginationDTO
{
    /**
     * @param array<mixed> $items
     */
    public function __construct(
        public array $items,
        public int $total,
        public int $limit,
        public int $offset,
    ) {}

    public function hasMore(): bool
    {
        return ($this->offset + $this->limit) < $this->total;
    }
}
