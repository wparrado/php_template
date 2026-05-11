<?php

declare(strict_types=1);

use Application\Commands\Item\CreateItemCommand;
use Application\Commands\Item\DeleteItemCommand;
use Application\Handlers\Item\CreateItemHandler;
use Application\Handlers\Item\DeleteItemHandler;
use Application\Mappers\ItemMapper;
use Application\Result\Failure;
use Application\Result\Success;
use Infrastructure\Clock\FakeClock;
use Infrastructure\Events\SyncEventPublisher;
use Infrastructure\Persistence\InMemory\InMemoryItemRepository;
use Infrastructure\Persistence\InMemory\InMemoryUnitOfWork;

beforeEach(function (): void {
    $this->repository = new InMemoryItemRepository();
    $this->uow = new InMemoryUnitOfWork();
    $this->clock = new FakeClock(new \DateTimeImmutable('2024-06-01T00:00:00Z'));
    $dispatcher = new \Illuminate\Events\Dispatcher();
    $this->eventPublisher = new SyncEventPublisher($dispatcher);

    $this->createHandler = new CreateItemHandler(
        repository: $this->repository,
        eventPublisher: $this->eventPublisher,
        uow: $this->uow,
        clock: $this->clock,
        mapper: new ItemMapper(),
    );

    $this->deleteHandler = new DeleteItemHandler(
        repository: $this->repository,
        eventPublisher: $this->eventPublisher,
        uow: $this->uow,
        clock: $this->clock,
    );
});

it('soft-deletes an existing item', function (): void {
    $createResult = $this->createHandler->handle(
        new CreateItemCommand('Widget', 10.0, 'USD', null, null)
    );
    $dto = $createResult->getValue();

    $result = $this->deleteHandler->handle(new DeleteItemCommand(itemId: $dto->id));

    expect($result)->toBeInstanceOf(Success::class);

    $item = $this->repository->findById($dto->id);
    expect($item)->not->toBeNull()
        ->and($item->isDeleted())->toBeTrue();

    // Should not appear in active count
    expect($this->repository->count())->toBe(0);
});

it('returns Failure when item does not exist', function (): void {
    $result = $this->deleteHandler->handle(
        new DeleteItemCommand(itemId: '00000000-0000-0000-0000-000000000000')
    );

    expect($result)->toBeInstanceOf(Failure::class);
});
