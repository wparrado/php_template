<?php

declare(strict_types=1);

use Application\Commands\Item\CreateItemCommand;
use Application\Commands\Item\UpdateItemCommand;
use Application\DTOs\ItemOutputDTO;
use Application\Handlers\Item\CreateItemHandler;
use Application\Handlers\Item\UpdateItemHandler;
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
    $this->mapper = new ItemMapper();

    $this->createHandler = new CreateItemHandler(
        repository: $this->repository,
        eventPublisher: $this->eventPublisher,
        uow: $this->uow,
        clock: $this->clock,
        mapper: $this->mapper,
    );

    $this->updateHandler = new UpdateItemHandler(
        repository: $this->repository,
        eventPublisher: $this->eventPublisher,
        uow: $this->uow,
        clock: $this->clock,
        mapper: $this->mapper,
    );
});

it('updates an existing item successfully', function (): void {
    // Create first
    $createResult = $this->createHandler->handle(
        new CreateItemCommand('Original', 5.0, 'USD', null, null)
    );
    $originalDto = $createResult->getValue();

    // Update
    $this->clock->advance(new \DateInterval('PT1M'));
    $result = $this->updateHandler->handle(new UpdateItemCommand(
        itemId: $originalDto->id,
        name: 'Updated',
        price: 15.0,
        currency: 'EUR',
        description: 'new desc',
        categoryId: null,
    ));

    expect($result)->toBeInstanceOf(Success::class);
    /** @var ItemOutputDTO $dto */
    $dto = $result->getValue();
    expect($dto->name)->toBe('Updated')
        ->and($dto->price)->toBe(15.0)
        ->and($dto->currency)->toBe('EUR');
});

it('returns Failure when item does not exist', function (): void {
    $result = $this->updateHandler->handle(new UpdateItemCommand(
        itemId: '00000000-0000-0000-0000-000000000000',
        name: 'Updated',
        price: 10.0,
        currency: 'USD',
        description: null,
        categoryId: null,
    ));

    expect($result)->toBeInstanceOf(Failure::class);
});
