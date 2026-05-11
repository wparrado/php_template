<?php

declare(strict_types=1);

namespace Domain\Model\Example\Events;

use Domain\Events\DomainEvent;

final class CategoryCreated extends DomainEvent
{
    public function __construct(
        string $aggregateId,
        public readonly string $name,
        public readonly ?string $description,
    ) {
        parent::__construct($aggregateId);
    }

    public function getEventName(): string
    {
        return 'category.created';
    }

    /** @return array<string, mixed> */
    public function getPayload(): array
    {
        return [
            'category_id' => $this->aggregateId,
            'name' => $this->name,
            'description' => $this->description,
            'occurred_at' => $this->getOccurredAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}
