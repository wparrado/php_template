<?php

declare(strict_types=1);

namespace Application\Handlers\Item;

use Application\Commands\Item\CreateItemCommand;
use Application\Mappers\ItemMapper;
use Application\Ports\UnitOfWorkInterface;
use Application\Result\Failure;
use Application\Result\Result;
use Application\Result\Success;
use Domain\Model\Example\Item;
use Domain\Ports\Inbound\ClockInterface;
use Domain\Ports\Outbound\EventPublisherInterface;
use Domain\Ports\Outbound\ItemRepositoryInterface;

final class CreateItemHandler
{
    public function __construct(
        private readonly ItemRepositoryInterface $repository,
        private readonly EventPublisherInterface $eventPublisher,
        private readonly UnitOfWorkInterface $uow,
        private readonly ClockInterface $clock,
        private readonly ItemMapper $mapper,
    ) {}

    public function handle(CreateItemCommand $command): Result
    {
        try {
            $this->uow->begin();

            $item = Item::create(
                name: $command->name,
                price: $command->price,
                currency: $command->currency,
                description: $command->description,
                categoryId: $command->categoryId,
                now: $this->clock->now(),
            );

            $this->repository->save($item);
            $events = $item->collectEvents();

            $this->uow->commit();

            $this->eventPublisher->publish($events);

            return new Success($this->mapper->toDTO($item));
        } catch (\Throwable $e) {
            $this->uow->rollback();

            return new Failure($e);
        }
    }
}
