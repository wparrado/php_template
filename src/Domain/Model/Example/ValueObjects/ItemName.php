<?php

declare(strict_types=1);

namespace Domain\Model\Example\ValueObjects;

use Domain\Exceptions\DomainException;
use Domain\Model\ValueObject;

final class ItemName extends ValueObject
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new class('Item name cannot be empty.') extends DomainException {};
        }

        if (strlen($trimmed) > 200) {
            throw new class('Item name cannot exceed 200 characters.') extends DomainException {};
        }

        $this->value = $trimmed;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
