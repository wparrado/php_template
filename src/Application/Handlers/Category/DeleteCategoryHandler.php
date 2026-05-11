<?php

declare(strict_types=1);

namespace Application\Handlers\Category;

use Application\Commands\Category\DeleteCategoryCommand;
use Application\Ports\UnitOfWorkInterface;
use Application\Result\Failure;
use Application\Result\Result;
use Application\Result\Success;
use Domain\Exceptions\CategoryNotFoundException;
use Domain\Ports\Outbound\CategoryRepositoryInterface;
use Domain\Ports\Outbound\EventPublisherInterface;

final class DeleteCategoryHandler
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository,
        private readonly EventPublisherInterface $eventPublisher,
        private readonly UnitOfWorkInterface $uow,
    ) {}

    public function handle(DeleteCategoryCommand $command): Result
    {
        try {
            $this->uow->begin();

            $category = $this->repository->findById($command->categoryId);

            if ($category === null) {
                throw CategoryNotFoundException::withId($command->categoryId);
            }

            $this->repository->delete($command->categoryId);
            $events = $category->collectEvents();

            $this->uow->commit();

            $this->eventPublisher->publish($events);

            return new Success(null);
        } catch (\Throwable $e) {
            $this->uow->rollback();

            return new Failure($e);
        }
    }
}
