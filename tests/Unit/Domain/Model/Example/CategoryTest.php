<?php

declare(strict_types=1);

use Domain\Exceptions\DomainException;
use Domain\Model\Example\Category;
use Domain\Model\Example\Events\CategoryCreated;
use Infrastructure\Clock\FakeClock;

beforeEach(function (): void {
    $this->clock = new FakeClock(new DateTimeImmutable('2024-06-01T10:00:00Z'));
});

describe('Category::create()', function (): void {
    it('creates a category with valid data', function (): void {
        $category = Category::create(
            name: 'Electronics',
            description: 'Electronic goods',
            now: $this->clock->now(),
        );

        expect($category->getName()->getValue())->toBe('Electronics')
            ->and($category->getDescription()->getValue())->toBe('Electronic goods');
    });

    it('records a CategoryCreated event on creation', function (): void {
        $category = Category::create('Electronics', null, $this->clock->now());

        $events = $category->collectEvents();

        expect($events)->toHaveCount(1)
            ->and($events[0])->toBeInstanceOf(CategoryCreated::class)
            ->and($events[0]->name)->toBe('Electronics');
    });

    it('allows null description', function (): void {
        $category = Category::create('Books', null, $this->clock->now());

        expect($category->getDescription()->isEmpty())->toBeTrue();
    });

    it('throws when name is empty', function (): void {
        Category::create('', null, $this->clock->now());
    })->throws(DomainException::class);
});

describe('Category::update()', function (): void {
    it('updates category fields', function (): void {
        $category = Category::create('Old', 'old desc', $this->clock->now());
        $category->collectEvents();

        $category->update('New', 'new desc', $this->clock->now());

        expect($category->getName()->getValue())->toBe('New')
            ->and($category->getDescription()->getValue())->toBe('new desc');
    });
});
