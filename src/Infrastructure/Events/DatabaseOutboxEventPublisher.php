<?php

declare(strict_types=1);

namespace Infrastructure\Events;

use Domain\Events\DomainEvent;
use Domain\Ports\Outbound\EventPublisherInterface;
use Illuminate\Support\Facades\DB;

/**
 * Transactional Outbox pattern: persists events to a DB table inside the same
 * transaction as the aggregate save, ensuring at-least-once delivery.
 * A separate worker (queue job) polls the outbox and forwards events to the broker.
 */
final class DatabaseOutboxEventPublisher implements EventPublisherInterface
{
    /**
     * @param  DomainEvent[]  $events
     */
    public function publish(array $events): void
    {
        foreach ($events as $event) {
            DB::table('outbox_events')->insert([
                'id' => $event->getEventId()->toString(),
                'event_name' => $event->getEventName(),
                'aggregate_id' => $event->aggregateId,
                'payload' => json_encode($event->getPayload(), JSON_THROW_ON_ERROR),
                'occurred_at' => $event->getOccurredAt()->format('Y-m-d H:i:s'),
                'published_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
