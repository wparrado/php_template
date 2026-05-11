<?php

declare(strict_types=1);

use Domain\Exceptions\DomainException;
use Domain\Model\Example\Events\ItemCreated;
use Domain\Model\Example\Events\ItemDeleted;
use Domain\Model\Example\Events\ItemUpdated;
use Domain\Model\Example\Item;
use Infrastructure\Clock\FakeClock;

beforeEach(function (): void {
    $this->clock = new FakeClock(new \DateTimeImmutable('2024-06-01T10:00:00Z'));
});

describe('Item::create()', function (): void {
    it('creates an item with valid data', function (): void {
        $item = Item::create(
            name: 'Test Widget',
            price: 19.99,
            currency: 'USD',
            description: 'A test widget',
            categoryId: null,
            now: $this->clock->now(),
        );

        expect($item->getName()->getValue())->toBe('Test Widget')
            ->and($item->getPrice()->getAmount())->toBe(19.99)
            ->and($item->getPrice()->getCurrency())->toBe('USD')
            ->and($item->getDescription()->getValue())->toBe('A test widget')
            ->and($item->isDeleted())->toBeFalse();
    });

    it('records an ItemCreated event on creation', function (): void {
        $item = Item::create(
            name: 'Widget',
            price: 10.0,
            currency: 'USD',
            description: null,
            categoryId: null,
            now: $this->clock->now(),
        );

        $events = $item->collectEvents();

        expect($events)->toHaveCount(1)
            ->and($events[0])->toBeInstanceOf(ItemCreated::class)
            ->and($events[0]->name)->toBe('Widget');
    });

    it('clears events after collecting them', function (): void {
        $item = Item::create('Widget', 10.0, 'USD', null, null, $this->clock->now());
        $item->collectEvents();

        expect($item->collectEvents())->toBeEmpty();
    });

    it('throws when name is empty', function (): void {
        Item::create('', 10.0, 'USD', null, null, $this->clock->now());
    })->throws(DomainException::class);

    it('throws when name exceeds 200 characters', function (): void {
        Item::create(str_repeat('a', 201), 10.0, 'USD', null, null, $this->clock->now());
    })->throws(DomainException::class);

    it('throws when price is negative', function (): void {
        Item::create('Widget', -1.0, 'USD', null, null, $this->clock->now());
    })->throws(DomainException::class);
});

describe('Item::update()', function (): void {
    it('updates item fields and records ItemUpdated event', function (): void {
        $item = Item::create('Old Name', 5.0, 'USD', 'old desc', null, $this->clock->now());
        $item->collectEvents(); // clear creation event

        $this->clock->advance(new \DateInterval('PT1H'));

        $item->update(
            name: 'New Name',
            price: 9.99,
            currency: 'EUR',
            description: 'new desc',
            categoryId: null,
            now: $this->clock->now(),
        );

        expect($item->getName()->getValue())->toBe('New Name')
            ->and($item->getPrice()->getAmount())->toBe(9.99)
            ->and($item->getPrice()->getCurrency())->toBe('EUR');

        $events = $item->collectEvents();
        expect($events)->toHaveCount(1)
            ->and($events[0])->toBeInstanceOf(ItemUpdated::class)
            ->and($events[0]->name)->toBe('New Name');
    });
});

describe('Item::markDeleted()', function (): void {
    it('marks item as deleted and records ItemDeleted event', function (): void {
        $item = Item::create('Widget', 10.0, 'USD', null, null, $this->clock->now());
        $item->collectEvents();

        $item->markDeleted($this->clock->now());

        expect($item->isDeleted())->toBeTrue();

        $events = $item->collectEvents();
        expect($events)->toHaveCount(1)
            ->and($events[0])->toBeInstanceOf(ItemDeleted::class);
    });
});
