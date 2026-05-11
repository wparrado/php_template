<?php

declare(strict_types=1);

namespace Application\Ports;

use Application\Commands\Category\CreateCategoryCommand;
use Application\Commands\Category\DeleteCategoryCommand;
use Application\Commands\Category\UpdateCategoryCommand;
use Application\Queries\Category\GetCategoryQuery;
use Application\Queries\Category\ListCategoriesQuery;
use Application\Result\Result;

interface CategoryApplicationServiceInterface
{
    public function create(CreateCategoryCommand $command): Result;

    public function update(UpdateCategoryCommand $command): Result;

    public function delete(DeleteCategoryCommand $command): Result;

    public function getById(GetCategoryQuery $query): Result;

    public function list(ListCategoriesQuery $query): Result;
}
