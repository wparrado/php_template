<?php

declare(strict_types=1);

namespace Infrastructure\Clock;

use DateTimeImmutable;
use Domain\Ports\Inbound\ClockInterface;

/**
 * Deterministic clock for testing — returns the configured time on every call.
 */
final class FakeClock implements ClockInterface
{
    private DateTimeImmutable $current;

    public function __construct(DateTimeImmutable $initial = new DateTimeImmutable('2024-01-01T00:00:00Z'))
    {
        $this->current = $initial;
    }

    public function now(): DateTimeImmutable
    {
        return $this->current;
    }

    public function advance(\DateInterval $interval): void
    {
        $this->current = $this->current->add($interval);
    }

    public function set(DateTimeImmutable $time): void
    {
        $this->current = $time;
    }
}
