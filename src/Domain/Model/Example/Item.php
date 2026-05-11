<?php

declare(strict_types=1);

namespace Domain\Model\Example;

use DateTimeImmutable;
use Domain\Model\AggregateRoot;
use Domain\Model\Example\Events\ItemCreated;
use Domain\Model\Example\Events\ItemDeleted;
use Domain\Model\Example\Events\ItemUpdated;
use Domain\Model\Example\ValueObjects\Description;
use Domain\Model\Example\ValueObjects\ItemName;
use Domain\Model\Example\ValueObjects\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class Item extends AggregateRoot
{
    private bool $deleted = false;

    private function __construct(
        UuidInterface $id,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        private ItemName $name,
        private Money $price,
        private Description $description,
        private ?UuidInterface $categoryId,
    ) {
        parent::__construct($id, $createdAt, $updatedAt);
    }

    /**
     * Factory method — creates a new Item and records ItemCreated event.
     */
    public static function create(
        string $name,
        float $price,
        string $currency,
        ?string $description,
        ?string $categoryId,
        DateTimeImmutable $now,
    ): self {
        $id = Uuid::uuid4();
        $itemName = ItemName::fromString($name);
        $money = Money::of($price, $currency);
        $desc = Description::fromString($description);
        $catId = $categoryId !== null ? Uuid::fromString($categoryId) : null;

        $item = new self(
            id: $id,
            createdAt: $now,
            updatedAt: $now,
            name: $itemName,
            price: $money,
            description: $desc,
            categoryId: $catId,
        );

        $item->recordEvent(new ItemCreated(
            aggregateId: $id->toString(),
            name: $itemName->getValue(),
            price: $money->getAmount(),
            currency: $money->getCurrency(),
            description: $desc->getValue(),
            categoryId: $catId?->toString(),
        ));

        return $item;
    }

    /**
     * Reconstitute an Item from persistence (no events emitted).
     */
    public static function reconstitute(
        string $id,
        string $name,
        float $price,
        string $currency,
        ?string $description,
        ?string $categoryId,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        bool $deleted = false,
    ): self {
        $item = new self(
            id: Uuid::fromString($id),
            createdAt: $createdAt,
            updatedAt: $updatedAt,
            name: ItemName::fromString($name),
            price: Money::of($price, $currency),
            description: Description::fromString($description),
            categoryId: $categoryId !== null ? Uuid::fromString($categoryId) : null,
        );
        $item->deleted = $deleted;

        return $item;
    }

    /**
     * Update the item's mutable fields and emit ItemUpdated event.
     */
    public function update(
        string $name,
        float $price,
        string $currency,
        ?string $description,
        ?string $categoryId,
        DateTimeImmutable $now,
    ): void {
        $this->name = ItemName::fromString($name);
        $this->price = Money::of($price, $currency);
        $this->description = Description::fromString($description);
        $this->categoryId = $categoryId !== null ? Uuid::fromString($categoryId) : null;
        $this->touch($now);

        $this->recordEvent(new ItemUpdated(
            aggregateId: $this->id->toString(),
            name: $this->name->getValue(),
            price: $this->price->getAmount(),
            currency: $this->price->getCurrency(),
            description: $this->description->getValue(),
            categoryId: $this->categoryId?->toString(),
        ));
    }

    /**
     * Soft-delete the item and emit ItemDeleted event.
     */
    public function markDeleted(DateTimeImmutable $now): void
    {
        $this->deleted = true;
        $this->touch($now);
        $this->recordEvent(new ItemDeleted($this->id->toString()));
    }

    public function getName(): ItemName
    {
        return $this->name;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function getDescription(): Description
    {
        return $this->description;
    }

    public function getCategoryId(): ?UuidInterface
    {
        return $this->categoryId;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }
}
