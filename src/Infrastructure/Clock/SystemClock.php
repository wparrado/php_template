<?php

declare(strict_types=1);

namespace Infrastructure\Clock;

use DateTimeImmutable;
use Domain\Ports\Inbound\ClockInterface;

final class SystemClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable;
    }
}
