<?php

declare(strict_types=1);

namespace Domain\Model;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

abstract class Entity
{
    public function __construct(
        protected readonly UuidInterface $id,
        protected readonly DateTimeImmutable $createdAt,
        protected DateTimeImmutable $updatedAt,
    ) {}

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    protected function touch(DateTimeImmutable $now): void
    {
        $this->updatedAt = $now;
    }

    public function equals(self $other): bool
    {
        return $this->id->equals($other->id);
    }
}
