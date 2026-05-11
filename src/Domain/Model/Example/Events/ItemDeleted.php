<?php

declare(strict_types=1);

namespace Domain\Model\Example\Events;

use Domain\Events\DomainEvent;

final class ItemDeleted extends DomainEvent
{
    public function __construct(string $aggregateId)
    {
        parent::__construct($aggregateId);
    }

    public function getEventName(): string
    {
        return 'item.deleted';
    }

    /** @return array<string, mixed> */
    public function getPayload(): array
    {
        return [
            'item_id' => $this->aggregateId,
            'occurred_at' => $this->getOccurredAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}
