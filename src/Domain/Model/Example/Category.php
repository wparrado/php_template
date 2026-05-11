<?php

declare(strict_types=1);

namespace Domain\Model\Example;

use DateTimeImmutable;
use Domain\Model\AggregateRoot;
use Domain\Model\Example\Events\CategoryCreated;
use Domain\Model\Example\ValueObjects\Description;
use Domain\Model\Example\ValueObjects\ItemName;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class Category extends AggregateRoot
{
    private function __construct(
        UuidInterface $id,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        private ItemName $name,
        private Description $description,
    ) {
        parent::__construct($id, $createdAt, $updatedAt);
    }

    /**
     * Factory method — creates a new Category and records CategoryCreated event.
     */
    public static function create(
        string $name,
        ?string $description,
        DateTimeImmutable $now,
    ): self {
        $id = Uuid::uuid4();
        $catName = ItemName::fromString($name);
        $desc = Description::fromString($description);

        $category = new self(
            id: $id,
            createdAt: $now,
            updatedAt: $now,
            name: $catName,
            description: $desc,
        );

        $category->recordEvent(new CategoryCreated(
            aggregateId: $id->toString(),
            name: $catName->getValue(),
            description: $desc->getValue(),
        ));

        return $category;
    }

    /**
     * Reconstitute a Category from persistence (no events emitted).
     */
    public static function reconstitute(
        string $id,
        string $name,
        ?string $description,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            id: Uuid::fromString($id),
            createdAt: $createdAt,
            updatedAt: $updatedAt,
            name: ItemName::fromString($name),
            description: Description::fromString($description),
        );
    }

    /**
     * Update the category's mutable fields.
     */
    public function update(
        string $name,
        ?string $description,
        DateTimeImmutable $now,
    ): void {
        $this->name = ItemName::fromString($name);
        $this->description = Description::fromString($description);
        $this->touch($now);
    }

    public function getName(): ItemName
    {
        return $this->name;
    }

    public function getDescription(): Description
    {
        return $this->description;
    }
}
