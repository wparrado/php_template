<?php

declare(strict_types=1);

namespace Domain\Specifications;

abstract class Specification
{
    abstract public function isSatisfiedBy(mixed $candidate): bool;

    public function and(self $other): self
    {
        return new class($this, $other) extends Specification
        {
            public function __construct(
                private readonly Specification $left,
                private readonly Specification $right,
            ) {}

            public function isSatisfiedBy(mixed $candidate): bool
            {
                return $this->left->isSatisfiedBy($candidate)
                    && $this->right->isSatisfiedBy($candidate);
            }
        };
    }

    public function or(self $other): self
    {
        return new class($this, $other) extends Specification
        {
            public function __construct(
                private readonly Specification $left,
                private readonly Specification $right,
            ) {}

            public function isSatisfiedBy(mixed $candidate): bool
            {
                return $this->left->isSatisfiedBy($candidate)
                    || $this->right->isSatisfiedBy($candidate);
            }
        };
    }

    public function not(): self
    {
        return new class($this) extends Specification
        {
            public function __construct(
                private readonly Specification $inner,
            ) {}

            public function isSatisfiedBy(mixed $candidate): bool
            {
                return ! $this->inner->isSatisfiedBy($candidate);
            }
        };
    }
}
