<?php

declare(strict_types=1);

namespace Application\Result;

final class Failure extends Result
{
    public function __construct(
        public \Throwable $error,
    ) {}

    public function isSuccess(): bool
    {
        return false;
    }

    public function isFailure(): bool
    {
        return true;
    }

    public function getError(): \Throwable
    {
        return $this->error;
    }

    public function getValue(): mixed
    {
        return null;
    }
}
