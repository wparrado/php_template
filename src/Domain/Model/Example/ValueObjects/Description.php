<?php

declare(strict_types=1);

namespace Domain\Model\Example\ValueObjects;

use Domain\Exceptions\DomainException;
use Domain\Model\ValueObject;

final class Description extends ValueObject
{
    private readonly ?string $value;

    public function __construct(?string $value)
    {
        if ($value !== null && strlen($value) > 1000) {
            throw new class('Description cannot exceed 1000 characters.') extends DomainException {};
        }

        $this->value = ($value !== null && trim($value) === '') ? null : $value;
    }

    public static function fromString(?string $value): self
    {
        return new self($value);
    }

    public static function empty(): self
    {
        return new self(null);
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return $this->value === null;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function toString(): string
    {
        return $this->value ?? '';
    }
}
