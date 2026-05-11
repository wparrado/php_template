<?php

declare(strict_types=1);

namespace Domain\Ports\Outbound;

use Domain\Events\DomainEvent;

interface EventPublisherInterface
{
    /**
     * @param DomainEvent[] $events
     */
    public function publish(array $events): void;
}
