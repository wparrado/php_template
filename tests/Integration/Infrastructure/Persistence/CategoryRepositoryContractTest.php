<?php

declare(strict_types=1);

use Domain\Model\Example\Category;
use Domain\Ports\Outbound\CategoryRepositoryInterface;
use Infrastructure\Clock\FakeClock;
use Infrastructure\Persistence\InMemory\InMemoryCategoryRepository;

/**
 * Contract test: runs the same assertions against EVERY CategoryRepository implementation.
 */
dataset('category repositories', [
    'InMemory' => fn () => new InMemoryCategoryRepository(),
]);

beforeEach(function (): void {
    $this->clock = new FakeClock(new \DateTimeImmutable('2024-01-01T00:00:00Z'));
});

function makeCategory(FakeClock $clock, string $name = 'Electronics'): Category
{
    return Category::create($name, null, $clock->now());
}

it('saves and retrieves a category by id', function (CategoryRepositoryInterface $repo): void {
    $category = makeCategory($this->clock);
    $category->collectEvents();

    $repo->save($category);

    $found = $repo->findById($category->getId()->toString());
    expect($found)->not->toBeNull()
        ->and($found->getName()->getValue())->toBe('Electronics');
})->with('category repositories');

it('returns null for a non-existent id', function (CategoryRepositoryInterface $repo): void {
    $result = $repo->findById('00000000-0000-0000-0000-000000000000');

    expect($result)->toBeNull();
})->with('category repositories');

it('findAll returns all categories with pagination', function (CategoryRepositoryInterface $repo): void {
    $repo->save(makeCategory($this->clock, 'A'));
    $repo->save(makeCategory($this->clock, 'B'));
    $repo->save(makeCategory($this->clock, 'C'));

    $page1 = $repo->findAll(2, 0);
    expect($page1)->toHaveCount(2);

    $page2 = $repo->findAll(2, 2);
    expect($page2)->toHaveCount(1);
})->with('category repositories');

it('delete removes the category from storage', function (CategoryRepositoryInterface $repo): void {
    $category = makeCategory($this->clock);
    $repo->save($category);
    $repo->delete($category->getId()->toString());

    expect($repo->findById($category->getId()->toString()))->toBeNull();
    expect($repo->count())->toBe(0);
})->with('category repositories');
