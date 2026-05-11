<?php

declare(strict_types=1);

namespace Application\Ports;

use Application\Commands\Item\CreateItemCommand;
use Application\Commands\Item\DeleteItemCommand;
use Application\Commands\Item\UpdateItemCommand;
use Application\DTOs\ItemOutputDTO;
use Application\DTOs\PaginationDTO;
use Application\Queries\Item\GetItemQuery;
use Application\Queries\Item\ListItemsQuery;
use Application\Result\Result;

interface ItemApplicationServiceInterface
{
    public function create(CreateItemCommand $command): Result;

    public function update(UpdateItemCommand $command): Result;

    public function delete(DeleteItemCommand $command): Result;

    public function getById(GetItemQuery $query): Result;

    public function list(ListItemsQuery $query): Result;
}
