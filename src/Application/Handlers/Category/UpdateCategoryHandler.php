<?php

declare(strict_types=1);

namespace Application\Handlers\Category;

use Application\Commands\Category\UpdateCategoryCommand;
use Application\Mappers\CategoryMapper;
use Application\Ports\UnitOfWorkInterface;
use Application\Result\Failure;
use Application\Result\Result;
use Application\Result\Success;
use Domain\Exceptions\CategoryNotFoundException;
use Domain\Ports\Inbound\ClockInterface;
use Domain\Ports\Outbound\CategoryRepositoryInterface;
use Domain\Ports\Outbound\EventPublisherInterface;

final class UpdateCategoryHandler
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository,
        private readonly EventPublisherInterface $eventPublisher,
        private readonly UnitOfWorkInterface $uow,
        private readonly ClockInterface $clock,
        private readonly CategoryMapper $mapper,
    ) {}

    public function handle(UpdateCategoryCommand $command): Result
    {
        try {
            $this->uow->begin();

            $category = $this->repository->findById($command->categoryId);

            if ($category === null) {
                throw CategoryNotFoundException::withId($command->categoryId);
            }

            $category->update(
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
