<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent;

use Application\Ports\UnitOfWorkInterface;
use Illuminate\Support\Facades\DB;

final class EloquentUnitOfWork implements UnitOfWorkInterface
{
    private int $depth = 0;

    public function begin(): void
    {
        if ($this->depth === 0) {
            DB::beginTransaction();
        }
        $this->depth++;
    }

    public function commit(): void
    {
        $this->depth--;
        if ($this->depth === 0) {
            DB::commit();
        }
    }

    public function rollback(): void
    {
        $this->depth = 0;
        DB::rollBack();
    }
}
