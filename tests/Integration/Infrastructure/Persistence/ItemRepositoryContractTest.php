<?php

declare(strict_types=1);

use Domain\Model\Example\Item;
use Domain\Ports\Outbound\ItemRepositoryInterface;
use Infrastructure\Clock\FakeClock;
use Infrastructure\Persistence\InMemory\InMemoryItemRepository;

/**
 * Contract test: runs the same assertions against EVERY ItemRepository implementation.
 * Add new implementations to the dataset to automatically extend coverage.
 */
dataset('item repositories', [
    'InMemory' => fn () => new InMemoryItemRepository,
]);

beforeEach(function (): void {
    $this->clock = new FakeClock(new DateTimeImmutable('2024-01-01T00:00:00Z'));
});

function makeItem(FakeClock $clock, string $name = 'Test Item', float $price = 9.99): Item
{
    return Item::create($name, $price, 'USD', null, null, $clock->now());
}

it('saves and retrieves an item by id', function (ItemRepositoryInterface $repo): void {
    $item = makeItem($this->clock);
    $item->collectEvents();

    $repo->save($item);

    $found = $repo->findById($item->getId()->toString());
    expect($found)->not->toBeNull()
        ->and($found->getName()->getValue())->toBe('Test Item');
})->with('item repositories');

it('returns null for a non-existent id', function (ItemRepositoryInterface $repo): void {
    $result = $repo->findById('00000000-0000-0000-0000-000000000000');

    expect($result)->toBeNull();
})->with('item repositories');

it('findAll returns active items with pagination', function (ItemRepositoryInterface $repo): void {
    $a = makeItem($this->clock, 'Item A');
    $b = makeItem($this->clock, 'Item B');
    $c = makeItem($this->clock, 'Item C');

    $repo->save($a);
    $repo->save($b);
    $repo->save($c);

    // Soft-delete one
    $c->markDeleted($this->clock->now());
    $c->collectEvents();
    $repo->save($c);

    $all = $repo->findAll(10, 0);
    expect($all)->toHaveCount(2);
})->with('item repositories');

it('count excludes soft-deleted items', function (ItemRepositoryInterface $repo): void {
    $item = makeItem($this->clock);
    $repo->save($item);
    $item->markDeleted($this->clock->now());
    $item->collectEvents();
    $repo->save($item);

    expect($repo->count())->toBe(0);
})->with('item repositories');

it('delete removes the item from storage', function (ItemRepositoryInterface $repo): void {
    $item = makeItem($this->clock);
    $repo->save($item);
    $repo->delete($item->getId()->toString());

    expect($repo->findById($item->getId()->toString()))->toBeNull();
})->with('item repositories');
