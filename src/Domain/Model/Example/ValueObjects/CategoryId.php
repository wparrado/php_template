<?php

declare(strict_types=1);

namespace Domain\Model\Example\ValueObjects;

use Domain\Model\ValueObject;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class CategoryId extends ValueObject
{
    private readonly UuidInterface $value;

    public function __construct(UuidInterface $value)
    {
        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4());
    }

    public static function fromString(string $value): self
    {
        return new self(Uuid::fromString($value));
    }

    public function getValue(): UuidInterface
    {
        return $this->value;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self && $this->value->equals($other->value);
    }

    public function toString(): string
    {
        return $this->value->toString();
    }
}
