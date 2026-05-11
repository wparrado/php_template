# Architecture Decision Record

## Overview

This project implements **Hexagonal Architecture** (also known as Ports & Adapters), combined with CQRS and key DDD patterns. The goal is a codebase where business logic is completely isolated from infrastructure concerns.

## Layer Responsibilities

### Domain Layer (`src/Domain/`)

The innermost layer. Contains **zero** framework or library imports except `ramsey/uuid`.

- **Entities & Aggregates**: Business objects with identity. Aggregates are consistency boundaries.
- **Value Objects**: Immutable, self-validating. Equality by value, not reference.
- **Domain Events**: Facts that happened. Recorded by aggregates, published after commit.
- **Ports (interfaces)**: Contracts the domain defines but does not implement (`RepositoryInterface`, `ClockInterface`).
- **Specifications**: Composable predicate objects (`and()`, `or()`, `not()`).

### Application Layer (`src/Application/`)

Orchestrates use cases. Knows Domain; knows nothing about HTTP, Eloquent, or queues.

- **Commands / Queries**: Simple `readonly` data structs. Commands mutate; queries read.
- **Handlers**: One handler per command/query. Follows the Unit-of-Work pattern.
- **DTOs**: Read-side view models returned from queries. `readonly` structs.
- **Result type**: `Result<T>` = `Success<T> | Failure`. No exceptions for expected failures.

### Infrastructure Layer (`src/Infrastructure/`)

Adapters that implement Domain ports using framework machinery.

- **Eloquent Repositories**: Map between Eloquent models and Domain aggregates. Use `reconstitute()` (no events).
- **InMemory Repositories**: Used in tests and memory-backend mode.
- **Unit of Work**: `EloquentUnitOfWork` wraps `DB::transaction` with a depth counter for nesting.
- **Event Publishers**: `SyncEventPublisher` (in-process) or `DatabaseOutboxEventPublisher` (transactional outbox).
- **Clock**: `SystemClock` wraps `new DateTimeImmutable()`. `FakeClock` allows deterministic testing.
- **Composition Root**: `AppServiceProvider` is the ONLY file that binds interfaces to implementations.

### Presentation Layer (`src/Presentation/`)

HTTP adapters. No business logic here.

- Controllers call Application Services, unwrap `Result`, throw on `Failure`.
- `ApiExceptionHandler` maps Domain exceptions to HTTP status codes.
- Form Requests handle HTTP validation before the use case is invoked.

## Key Design Decisions

### Composition Root

`Infrastructure\Providers\AppServiceProvider` is the single place that wires interfaces to implementations. All other classes depend on interfaces only. This makes it trivial to swap backends.

### Dual Backend

`DB_BACKEND=memory` uses `InMemoryItemRepository` (a plain PHP array). No database setup needed. `DB_BACKEND=eloquent` uses `EloquentItemRepository` backed by PostgreSQL. Controlled via `config('app.db_backend')`.

### Unit of Work Pattern

Handlers follow this exact sequence:

```
uow.begin()
aggregate = repository.findById(id) OR aggregate = Aggregate::create(...)
aggregate.doSomething()
repository.save(aggregate)
events = aggregate.collectEvents()   // destructive read; clears the list
uow.commit()
eventPublisher.publish(events)        // publish AFTER commit
return Result::success(...)
```

Events are published **after** commit to avoid publishing on a rolled-back transaction.

### `create()` vs `reconstitute()`

- `Item::create()` — static factory. Records `ItemCreated` domain event.
- `Item::reconstitute()` — rehydrates from persistence. Fires NO events.

This is the standard DDD pattern: only brand-new aggregates fire creation events.

### `collectEvents()` is destructive

`collectEvents()` empties the aggregate's event list. Calling it twice returns an empty array the second time. Use `peekEvents()` in tests when you need non-destructive inspection.

### Transactional Outbox

When `EVENT_PUBLISHER=outbox`, `DatabaseOutboxEventPublisher` writes event payloads to the `outbox_events` table inside the same database transaction as the aggregate mutation. A separate worker (not included) polls this table for at-least-once delivery to downstream consumers.

### Value Object Validation

Value objects throw anonymous `DomainException` subclasses to avoid class explosion:

```php
throw new class("Invalid value: $value") extends DomainException {};
```

For exceptions that need to be caught by type in the exception handler (`ItemNotFoundException`), concrete exception classes are used.

## Enforced Boundaries

### Deptrac (`deptrac.yaml`)

Checks that `Domain\` classes import nothing from `Application\`, `Infrastructure\`, or `Presentation\`. Enforced in CI.

### PHPArkitect (`phparkitect.php`)

Same rules expressed as PHPArkitect `ArchRule` assertions. Run via `composer arkitect`.

### PHPStan Level 9

Full strict static analysis. The domain layer is the most important to keep clean here.

## Testing Strategy

```
tests/
├── Unit/
│   ├── Domain/       Pure domain tests — no framework, no DB
│   └── Application/  Handler tests — InMemory adapters only
├── Integration/
│   └── Infrastructure/  Contract tests parametrized over all implementations
└── Architecture/    Layer dependency assertions
```

Integration tests use the **parametrized dataset pattern**: add a new repository implementation to the `dataset()` call and all contract tests run against it automatically.
