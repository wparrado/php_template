<?php

declare(strict_types=1);

namespace Application\Result;

abstract class Result
{
    abstract public function isSuccess(): bool;

    abstract public function isFailure(): bool;
}
