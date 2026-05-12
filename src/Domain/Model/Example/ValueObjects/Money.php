<?php

declare(strict_types=1);

namespace Domain\Model\Example\ValueObjects;

use Domain\Exceptions\DomainException;
use Domain\Model\ValueObject;

final class Money extends ValueObject
{
    private readonly float $amount;

    private readonly string $currency;

    public function __construct(float $amount, string $currency = 'USD')
    {
        if ($amount < 0) {
            throw new class('Money amount cannot be negative.') extends DomainException {};
        }

        if (trim($currency) === '') {
            throw new class('Currency code cannot be empty.') extends DomainException {};
        }

        $this->amount = round($amount, 2);
        $this->currency = strtoupper(trim($currency));
    }

    public static function of(float $amount, string $currency = 'USD'): self
    {
        return new self($amount, $currency);
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && $this->amount === $other->amount
            && $this->currency === $other->currency;
    }

    public function toString(): string
    {
        return sprintf('%s %.2f', $this->currency, $this->amount);
    }
}
