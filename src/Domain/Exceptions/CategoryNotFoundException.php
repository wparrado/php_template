<?php

declare(strict_types=1);

namespace Domain\Exceptions;

final class CategoryNotFoundException extends DomainException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Category with ID "%s" was not found.', $id));
    }
}
