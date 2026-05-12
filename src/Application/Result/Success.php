<?php

declare(strict_types=1);

namespace Application\Result;

final class Success extends Result
{
    public function __construct(
        public readonly mixed $value,
    ) {}

    public function isSuccess(): bool
    {
        return true;
    }

    public function isFailure(): bool
    {
        return false;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
