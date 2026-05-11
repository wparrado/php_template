<?php

declare(strict_types=1);

namespace Domain\Events;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class DomainEvent
{
    private readonly UuidInterface $eventId;
    private readonly DateTimeImmutable $occurredAt;

    public function __construct(
        public readonly string $aggregateId,
    ) {
        $this->eventId = Uuid::uuid4();
        $this->occurredAt = new DateTimeImmutable();
    }

    public function getEventId(): UuidInterface
    {
        return $this->eventId;
    }

    public function getOccurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    abstract public function getEventName(): string;

    /** @return array<string, mixed> */
    abstract public function getPayload(): array;
}
