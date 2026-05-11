<?php

declare(strict_types=1);

namespace Application\Handlers\Category;

use Application\Commands\Category\CreateCategoryCommand;
use Application\Mappers\CategoryMapper;
use Application\Ports\UnitOfWorkInterface;
use Application\Result\Failure;
use Application\Result\Result;
use Application\Result\Success;
use Domain\Model\Example\Category;
use Domain\Ports\Inbound\ClockInterface;
use Domain\Ports\Outbound\CategoryRepositoryInterface;
use Domain\Ports\Outbound\EventPublisherInterface;

final class CreateCategoryHandler
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository,
        private readonly EventPublisherInterface $eventPublisher,
        private readonly UnitOfWorkInterface $uow,
        private readonly ClockInterface $clock,
        private readonly CategoryMapper $mapper,
    ) {}

    public function handle(CreateCategoryCommand $command): Result
    {
        try {
            $this->uow->begin();

            $category = Category::create(
                name: $command->name,
                description: $command->description,
                now: $this->clock->now(),
            );

            $this->repository->save($category);
            $events = $category->collectEvents();

            $this->uow->commit();

            $this->eventPublisher->publish($events);

            return new Success($this->mapper->toDTO($category));
        } catch (\Throwable $e) {
            $this->uow->rollback();

            return new Failure($e);
        }
    }
}
