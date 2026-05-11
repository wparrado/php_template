<?php

declare(strict_types=1);

namespace Domain\Ports\Inbound;

use DateTimeImmutable;

interface ClockInterface
{
    public function now(): DateTimeImmutable;
}
