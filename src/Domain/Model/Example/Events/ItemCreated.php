<?php

declare(strict_types=1);

namespace Domain\Model\Example\Events;

use Domain\Events\DomainEvent;

final class ItemCreated extends DomainEvent
{
    public function __construct(
        string $aggregateId,
        public readonly string $name,
        public readonly float $price,
        public readonly string $currency,
        public readonly ?string $description,
        public readonly ?string $categoryId,
    ) {
        parent::__construct($aggregateId);
    }

    public function getEventName(): string
    {
        return 'item.created';
    }

    /** @return array<string, mixed> */
    public function getPayload(): array
    {
        return [
            'item_id' => $this->aggregateId,
            'name' => $this->name,
            'price' => $this->price,
            'currency' => $this->currency,
            'description' => $this->description,
            'category_id' => $this->categoryId,
            'occurred_at' => $this->getOccurredAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}
