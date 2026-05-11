<?php

declare(strict_types=1);

namespace Domain\Model;

use Domain\Events\DomainEvent;

abstract class AggregateRoot extends Entity
{
    /** @var DomainEvent[] */
    private array $domainEvents = [];

    protected function recordEvent(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    /**
     * Returns and clears all recorded domain events.
     *
     * @return DomainEvent[]
     */
    public function collectEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }

    /**
     * Peek at events without clearing them (useful for testing).
     *
     * @return DomainEvent[]
     */
    public function peekEvents(): array
    {
        return $this->domainEvents;
    }
}
