<?php

declare(strict_types=1);

namespace Application\Handlers\Item;

use Application\DTOs\PaginationDTO;
use Application\Mappers\ItemMapper;
use Application\Queries\Item\ListItemsQuery;
use Application\Result\Failure;
use Application\Result\Result;
use Application\Result\Success;
use Domain\Ports\Outbound\ItemRepositoryInterface;

final class ListItemsHandler
{
    public function __construct(
        private readonly ItemRepositoryInterface $repository,
        private readonly ItemMapper $mapper,
    ) {}

    public function handle(ListItemsQuery $query): Result
    {
        try {
            $items = $this->repository->findAll($query->limit, $query->offset);
            $total = $this->repository->count();

            return new Success(new PaginationDTO(
                items: $this->mapper->toDTOList($items),
                total: $total,
                limit: $query->limit,
                offset: $query->offset,
            ));
        } catch (\Throwable $e) {
            return new Failure($e);
        }
    }
}
