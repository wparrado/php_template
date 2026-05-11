<?php

declare(strict_types=1);

namespace Application\Handlers\Category;

use Application\Mappers\CategoryMapper;
use Application\Queries\Category\GetCategoryQuery;
use Application\Result\Failure;
use Application\Result\Result;
use Application\Result\Success;
use Domain\Exceptions\CategoryNotFoundException;
use Domain\Ports\Outbound\CategoryRepositoryInterface;

final class GetCategoryHandler
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository,
        private readonly CategoryMapper $mapper,
    ) {}

    public function handle(GetCategoryQuery $query): Result
    {
        try {
            $category = $this->repository->findById($query->categoryId);

            if ($category === null) {
                throw CategoryNotFoundException::withId($query->categoryId);
            }

            return new Success($this->mapper->toDTO($category));
        } catch (\Throwable $e) {
            return new Failure($e);
        }
    }
}
