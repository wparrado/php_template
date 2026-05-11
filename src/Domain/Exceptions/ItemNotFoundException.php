<?php

declare(strict_types=1);

namespace Domain\Exceptions;

final class ItemNotFoundException extends DomainException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Item with ID "%s" was not found.', $id));
    }
}
