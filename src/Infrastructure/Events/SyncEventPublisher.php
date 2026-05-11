<?php

declare(strict_types=1);

namespace Infrastructure\Events;

use Domain\Events\DomainEvent;
use Domain\Ports\Outbound\EventPublisherInterface;
use Illuminate\Events\Dispatcher;

final class SyncEventPublisher implements EventPublisherInterface
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    /**
     * @param DomainEvent[] $events
     */
    public function publish(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatcher->dispatch($event->getEventName(), $event->getPayload());
        }
    }
}
