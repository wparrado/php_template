<?php

declare(strict_types=1);

namespace Application\Handlers\Category;

use Application\DTOs\PaginationDTO;
use Application\Mappers\CategoryMapper;
use Application\Queries\Category\ListCategoriesQuery;
use Application\Result\Failure;
use Application\Result\Result;
use Application\Result\Success;
use Domain\Ports\Outbound\CategoryRepositoryInterface;

final class ListCategoriesHandler
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository,
        private readonly CategoryMapper $mapper,
    ) {}

    public function handle(ListCategoriesQuery $query): Result
    {
        try {
            $categories = $this->repository->findAll($query->limit, $query->offset);
            $total = $this->repository->count();

            return new Success(new PaginationDTO(
                items: $this->mapper->toDTOList($categories),
                total: $total,
                limit: $query->limit,
                offset: $query->offset,
            ));
        } catch (\Throwable $e) {
            return new Failure($e);
        }
    }
}
