<?php

declare(strict_types=1);

namespace Application\Services;

use Application\Commands\Item\CreateItemCommand;
use Application\Commands\Item\DeleteItemCommand;
use Application\Commands\Item\UpdateItemCommand;
use Application\Handlers\Item\CreateItemHandler;
use Application\Handlers\Item\DeleteItemHandler;
use Application\Handlers\Item\GetItemHandler;
use Application\Handlers\Item\ListItemsHandler;
use Application\Handlers\Item\UpdateItemHandler;
use Application\Ports\ItemApplicationServiceInterface;
use Application\Queries\Item\GetItemQuery;
use Application\Queries\Item\ListItemsQuery;
use Application\Result\Result;

final class ItemApplicationService implements ItemApplicationServiceInterface
{
    public function __construct(
        private readonly CreateItemHandler $createHandler,
        private readonly UpdateItemHandler $updateHandler,
        private readonly DeleteItemHandler $deleteHandler,
        private readonly GetItemHandler $getHandler,
        private readonly ListItemsHandler $listHandler,
    ) {}

    public function create(CreateItemCommand $command): Result
    {
        return $this->createHandler->handle($command);
    }

    public function update(UpdateItemCommand $command): Result
    {
        return $this->updateHandler->handle($command);
    }

    public function delete(DeleteItemCommand $command): Result
    {
        return $this->deleteHandler->handle($command);
    }

    public function getById(GetItemQuery $query): Result
    {
        return $this->getHandler->handle($query);
    }

    public function list(ListItemsQuery $query): Result
    {
        return $this->listHandler->handle($query);
    }
}
