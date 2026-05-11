<?php

declare(strict_types=1);

namespace Application\Ports;

interface UnitOfWorkInterface
{
    public function begin(): void;

    public function commit(): void;

    public function rollback(): void;
}
