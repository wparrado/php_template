<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\InMemory;

use Application\Ports\UnitOfWorkInterface;

final class InMemoryUnitOfWork implements UnitOfWorkInterface
{
    private bool $active = false;

    public function begin(): void
    {
        $this->active = true;
    }

    public function commit(): void
    {
        $this->active = false;
    }

    public function rollback(): void
    {
        $this->active = false;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
