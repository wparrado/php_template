<?php

declare(strict_types=1);

namespace Application\Services;

use Application\Commands\Category\CreateCategoryCommand;
use Application\Commands\Category\DeleteCategoryCommand;
use Application\Commands\Category\UpdateCategoryCommand;
use Application\Handlers\Category\CreateCategoryHandler;
use Application\Handlers\Category\DeleteCategoryHandler;
use Application\Handlers\Category\GetCategoryHandler;
use Application\Handlers\Category\ListCategoriesHandler;
use Application\Handlers\Category\UpdateCategoryHandler;
use Application\Ports\CategoryApplicationServiceInterface;
use Application\Queries\Category\GetCategoryQuery;
use Application\Queries\Category\ListCategoriesQuery;
use Application\Result\Result;

final class CategoryApplicationService implements CategoryApplicationServiceInterface
{
    public function __construct(
        private readonly CreateCategoryHandler $createHandler,
        private readonly UpdateCategoryHandler $updateHandler,
        private readonly DeleteCategoryHandler $deleteHandler,
        private readonly GetCategoryHandler $getHandler,
        private readonly ListCategoriesHandler $listHandler,
    ) {}

    public function create(CreateCategoryCommand $command): Result
    {
        return $this->createHandler->handle($command);
    }

    public function update(UpdateCategoryCommand $command): Result
    {
        return $this->updateHandler->handle($command);
    }

    public function delete(DeleteCategoryCommand $command): Result
    {
        return $this->deleteHandler->handle($command);
    }

    public function getById(GetCategoryQuery $query): Result
    {
        return $this->getHandler->handle($query);
    }

    public function list(ListCategoriesQuery $query): Result
    {
        return $this->listHandler->handle($query);
    }
}
