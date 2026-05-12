<?php

declare(strict_types=1);

use Application\Commands\Item\CreateItemCommand;
use Application\DTOs\ItemOutputDTO;
use Application\Handlers\Item\CreateItemHandler;
use Application\Mappers\ItemMapper;
use Application\Result\Failure;
use Application\Result\Success;
use Illuminate\Events\Dispatcher;
use Infrastructure\Clock\FakeClock;
use Infrastructure\Events\SyncEventPublisher;
use Infrastructure\Persistence\InMemory\InMemoryItemRepository;
use Infrastructure\Persistence\InMemory\InMemoryUnitOfWork;

beforeEach(function (): void {
    $this->repository = new InMemoryItemRepository;
    $this->uow = new InMemoryUnitOfWork;
    $this->clock = new FakeClock(new DateTimeImmutable('2024-06-01T00:00:00Z'));
    $this->publishedEvents = [];

    // Capture published events for assertion
    $dispatcher = new Dispatcher;
    $this->eventPublisher = new SyncEventPublisher($dispatcher);

    $this->handler = new CreateItemHandler(
        repository: $this->repository,
        eventPublisher: $this->eventPublisher,
        uow: $this->uow,
        clock: $this->clock,
        mapper: new ItemMapper,
    );
});

it('returns Success with ItemOutputDTO when item is created', function (): void {
    $command = new CreateItemCommand(
        name: 'Widget Pro',
        price: 29.99,
        currency: 'USD',
        description: 'A professional widget',
        categoryId: null,
    );

    $result = $this->handler->handle($command);

    expect($result)->toBeInstanceOf(Success::class);

    /** @var ItemOutputDTO $dto */
    $dto = $result->getValue();
    expect($dto)->toBeInstanceOf(ItemOutputDTO::class)
        ->and($dto->name)->toBe('Widget Pro')
        ->and($dto->price)->toBe(29.99)
        ->and($dto->currency)->toBe('USD');
});

it('persists the item to the repository', function (): void {
    $command = new CreateItemCommand('Widget', 10.0, 'USD', null, null);

    $result = $this->handler->handle($command);
    $dto = $result->getValue();

    $saved = $this->repository->findById($dto->id);
    expect($saved)->not->toBeNull()
        ->and($saved->getName()->getValue())->toBe('Widget');
});

it('returns Failure when domain validation fails', function (): void {
    $command = new CreateItemCommand('', 10.0, 'USD', null, null);

    $result = $this->handler->handle($command);

    expect($result)->toBeInstanceOf(Failure::class);
});

it('rolls back uow on failure', function (): void {
    $command = new CreateItemCommand('', -1.0, 'USD', null, null);

    $this->handler->handle($command);

    // Repository should be empty
    expect($this->repository->count())->toBe(0);
});
