<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Application\Handlers\Category\CreateCategoryHandler;
use Application\Handlers\Category\DeleteCategoryHandler;
use Application\Handlers\Category\GetCategoryHandler;
use Application\Handlers\Category\ListCategoriesHandler;
use Application\Handlers\Category\UpdateCategoryHandler;
use Application\Handlers\Item\CreateItemHandler;
use Application\Handlers\Item\DeleteItemHandler;
use Application\Handlers\Item\GetItemHandler;
use Application\Handlers\Item\ListItemsHandler;
use Application\Handlers\Item\UpdateItemHandler;
use Application\Mappers\CategoryMapper;
use Application\Mappers\ItemMapper;
use Application\Ports\CategoryApplicationServiceInterface;
use Application\Ports\ItemApplicationServiceInterface;
use Application\Ports\UnitOfWorkInterface;
use Application\Services\CategoryApplicationService;
use Application\Services\ItemApplicationService;
use Domain\Ports\Inbound\ClockInterface;
use Domain\Ports\Outbound\CategoryRepositoryInterface;
use Domain\Ports\Outbound\EventPublisherInterface;
use Domain\Ports\Outbound\ItemRepositoryInterface;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Clock\SystemClock;
use Infrastructure\Events\DatabaseOutboxEventPublisher;
use Infrastructure\Events\SyncEventPublisher;
use Infrastructure\Persistence\Eloquent\CategoryRepository as EloquentCategoryRepository;
use Infrastructure\Persistence\Eloquent\EloquentUnitOfWork;
use Infrastructure\Persistence\Eloquent\ItemRepository as EloquentItemRepository;
use Infrastructure\Persistence\InMemory\InMemoryCategoryRepository;
use Infrastructure\Persistence\InMemory\InMemoryItemRepository;
use Infrastructure\Persistence\InMemory\InMemoryUnitOfWork;

/**
 * Composition Root — the ONLY place that wires interfaces to concrete implementations.
 *
 * Selection is driven by environment variables:
 *   DB_BACKEND    = memory | eloquent   (default: memory)
 *   EVENT_PUBLISHER = sync | outbox     (default: sync)
 */
final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->bindClock();
        $this->bindPersistence();
        $this->bindEventPublisher();
        $this->bindMappers();
        $this->bindHandlers();
        $this->bindApplicationServices();
    }

    public function boot(): void {}

    private function bindClock(): void
    {
        $this->app->singleton(ClockInterface::class, SystemClock::class);
    }

    private function bindPersistence(): void
    {
        $backend = config('app.db_backend', 'memory');

        if ($backend === 'eloquent') {
            $this->app->singleton(ItemRepositoryInterface::class, EloquentItemRepository::class);
            $this->app->singleton(CategoryRepositoryInterface::class, EloquentCategoryRepository::class);
            $this->app->singleton(UnitOfWorkInterface::class, EloquentUnitOfWork::class);
        } else {
            // Default: in-memory (no database required — great for development and unit tests)
            $this->app->singleton(ItemRepositoryInterface::class, InMemoryItemRepository::class);
            $this->app->singleton(CategoryRepositoryInterface::class, InMemoryCategoryRepository::class);
            $this->app->singleton(UnitOfWorkInterface::class, InMemoryUnitOfWork::class);
        }
    }

    private function bindEventPublisher(): void
    {
        $publisher = config('app.event_publisher', 'sync');

        if ($publisher === 'outbox') {
            $this->app->singleton(EventPublisherInterface::class, DatabaseOutboxEventPublisher::class);
        } else {
            $this->app->singleton(EventPublisherInterface::class, function () {
                return new SyncEventPublisher($this->app->make(Dispatcher::class));
            });
        }
    }

    private function bindMappers(): void
    {
        $this->app->singleton(ItemMapper::class);
        $this->app->singleton(CategoryMapper::class);
    }

    private function bindHandlers(): void
    {
        // Item handlers
        $this->app->singleton(CreateItemHandler::class);
        $this->app->singleton(UpdateItemHandler::class);
        $this->app->singleton(DeleteItemHandler::class);
        $this->app->singleton(GetItemHandler::class);
        $this->app->singleton(ListItemsHandler::class);

        // Category handlers
        $this->app->singleton(CreateCategoryHandler::class);
        $this->app->singleton(UpdateCategoryHandler::class);
        $this->app->singleton(DeleteCategoryHandler::class);
        $this->app->singleton(GetCategoryHandler::class);
        $this->app->singleton(ListCategoriesHandler::class);
    }

    private function bindApplicationServices(): void
    {
        $this->app->singleton(ItemApplicationServiceInterface::class, ItemApplicationService::class);
        $this->app->singleton(CategoryApplicationServiceInterface::class, CategoryApplicationService::class);
    }
}
