<?php

declare(strict_types=1);

namespace Application\Handlers\Item;

use Application\Mappers\ItemMapper;
use Application\Queries\Item\GetItemQuery;
use Application\Result\Failure;
use Application\Result\Result;
use Application\Result\Success;
use Domain\Exceptions\ItemNotFoundException;
use Domain\Ports\Outbound\ItemRepositoryInterface;

final class GetItemHandler
{
    public function __construct(
        private readonly ItemRepositoryInterface $repository,
        private readonly ItemMapper $mapper,
    ) {}

    public function handle(GetItemQuery $query): Result
    {
        try {
            $item = $this->repository->findById($query->itemId);

            if ($item === null) {
                throw ItemNotFoundException::withId($query->itemId);
            }

            return new Success($this->mapper->toDTO($item));
        } catch (\Throwable $e) {
            return new Failure($e);
        }
    }
}
