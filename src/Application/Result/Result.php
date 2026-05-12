<?php

declare(strict_types=1);

namespace Application\Result;

abstract readonly class Result
{
    abstract public function isSuccess(): bool;

    abstract public function isFailure(): bool;

    abstract public function getValue(): mixed;
}
