<?php

declare(strict_types=1);

namespace Application\Handlers\Item;

use Application\Commands\Item\DeleteItemCommand;
use Application\Ports\UnitOfWorkInterface;
use Application\Result\Failure;
use Application\Result\Result;
use Application\Result\Success;
use Domain\Exceptions\ItemNotFoundException;
use Domain\Ports\Inbound\ClockInterface;
use Domain\Ports\Outbound\EventPublisherInterface;
use Domain\Ports\Outbound\ItemRepositoryInterface;

final class DeleteItemHandler
{
    public function __construct(
        private readonly ItemRepositoryInterface $repository,
        private readonly EventPublisherInterface $eventPublisher,
        private readonly UnitOfWorkInterface $uow,
        private readonly ClockInterface $clock,
    ) {}

    public function handle(DeleteItemCommand $command): Result
    {
        try {
            $this->uow->begin();

            $item = $this->repository->findById($command->itemId);

            if ($item === null) {
                throw ItemNotFoundException::withId($command->itemId);
            }

            $item->markDeleted($this->clock->now());
            $this->repository->save($item);
            $events = $item->collectEvents();

            $this->uow->commit();

            $this->eventPublisher->publish($events);

            return new Success(null);
        } catch (\Throwable $e) {
            $this->uow->rollback();

            return new Failure($e);
        }
    }
}
