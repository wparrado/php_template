<?php

declare(strict_types=1);

namespace Domain\Model;

abstract class ValueObject
{
    abstract public function equals(self $other): bool;

    abstract public function toString(): string;

    public function __toString(): string
    {
        return $this->toString();
    }
}
