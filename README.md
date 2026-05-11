# PHP Laravel 11 — Hexagonal Architecture Template

A production-ready Laravel 11 template implementing **Hexagonal Architecture** (Ports & Adapters) with CQRS, Domain-Driven Design, and a full quality toolchain.

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                     Presentation Layer                      │
│          HTTP Controllers · Form Requests · Resources       │
└───────────────────────────┬─────────────────────────────────┘
                            │ Commands / Queries
┌───────────────────────────▼─────────────────────────────────┐
│                     Application Layer                       │
│     Command Handlers · Query Handlers · DTOs · Results      │
└──────────────┬────────────────────────┬─────────────────────┘
               │ Domain model           │ Ports (interfaces)
┌──────────────▼──────────────┐  ┌──────▼──────────────────────┐
│        Domain Layer         │  │    Infrastructure Layer      │
│  Aggregates · ValueObjects  │  │  Eloquent · InMemory · Clock │
│  Events · Specifications    │  │  EventPublisher · UoW        │
└─────────────────────────────┘  └─────────────────────────────┘
```

**Dependency rule**: arrows point inward only. Domain knows nothing of the other layers.

## Quick Start

### Memory mode (no database required)

```bash
cp .env.example .env
php artisan key:generate
DB_BACKEND=memory php artisan serve
```

### PostgreSQL mode

```bash
cp .env.example .env && php artisan key:generate
DB_BACKEND=eloquent php artisan migrate
php artisan serve
```

### Docker (memory mode, no dependencies)

```bash
docker compose up app-memory
```

### Docker with PostgreSQL

```bash
docker compose --profile postgres up
```

## Requirements

- PHP 8.3+
- Composer 2
- PostgreSQL 15+ (for Eloquent backend only)

## Environment Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `DB_BACKEND` | `memory` | `memory` or `eloquent` |
| `EVENT_PUBLISHER` | `sync` | `sync` or `outbox` |
| `APP_KEY` | — | Laravel app key (run `artisan key:generate`) |
| `DB_*` | — | Standard Laravel DB connection settings |

## API Endpoints

All endpoints are prefixed with `/api/v1/`.

| Method | URI | Action |
|--------|-----|--------|
| `GET` | `/api/health` | Health check |
| `GET` | `/api/v1/items` | List items (paginated) |
| `POST` | `/api/v1/items` | Create item |
| `GET` | `/api/v1/items/{id}` | Get item |
| `PUT` | `/api/v1/items/{id}` | Update item |
| `DELETE` | `/api/v1/items/{id}` | Delete item |
| `GET` | `/api/v1/categories` | List categories |
| `POST` | `/api/v1/categories` | Create category |
| `GET` | `/api/v1/categories/{id}` | Get category |
| `PUT` | `/api/v1/categories/{id}` | Update category |
| `DELETE` | `/api/v1/categories/{id}` | Delete category |

## Development

### Quality toolchain

```bash
# Format code
composer lint

# Check formatting (no changes)
composer lint:check

# Static analysis (PHPStan level 9 + Larastan)
composer stan

# Run all tests
composer test

# Run unit tests only
composer test:unit

# Check layer architecture
composer deptrac

# PHPArkitect rules
composer arkitect
```

### Install git hooks

```bash
vendor/bin/captainhook install
```

Hooks enforce: `pint` + `stan` on pre-commit, `test:unit` + `deptrac` on pre-push.

## Testing

Tests use **Pest PHP** and are split into three groups:

| Group | Command | Database needed? |
|-------|---------|-----------------|
| Unit | `composer test:unit` | ❌ No (InMemory) |
| Integration | `vendor/bin/pest tests/Integration` | ❌ No (InMemory) |
| Architecture | `vendor/bin/pest tests/Architecture` | ❌ No |

### Adding a new backend to contract tests

Open `tests/Integration/Infrastructure/Persistence/ItemRepositoryContractTest.php` and add your implementation to the `dataset`:

```php
dataset('item repositories', [
    'InMemory' => fn () => new InMemoryItemRepository(),
    'Eloquent' => fn () => new EloquentItemRepository(),  // add here
]);
```

## Project Structure

```
src/
├── Domain/          # Pure PHP — zero framework deps
│   ├── Model/
│   │   ├── Entity.php
│   │   ├── AggregateRoot.php
│   │   ├── ValueObject.php
│   │   └── Example/
│   │       ├── Item.php
│   │       ├── Category.php
│   │       ├── Events/
│   │       └── ValueObjects/
│   ├── Events/
│   ├── Exceptions/
│   ├── Ports/
│   └── Specifications/
├── Application/     # Use cases, handlers, DTOs
│   ├── Commands/
│   ├── Queries/
│   ├── Handlers/
│   ├── DTOs/
│   ├── Mappers/
│   ├── Ports/
│   ├── Result/
│   └── Services/
├── Infrastructure/  # Framework/DB/messaging adapters
│   ├── Clock/
│   ├── Events/
│   ├── Persistence/
│   │   ├── Eloquent/
│   │   └── InMemory/
│   └── Providers/   # ← Composition Root
└── Presentation/    # HTTP controllers, resources, middleware
    └── Http/
        ├── Controllers/
        ├── Middleware/
        └── Requests/
```

## Adding a New Aggregate

1. **Domain**: Create `src/Domain/Model/YourThing/YourThing.php` aggregate + value objects + events
2. **Ports**: Add `YourThingRepositoryInterface` in `src/Domain/Ports/Outbound/`
3. **Application**: Commands, Queries, Handlers, DTO, Mapper
4. **Infrastructure**: InMemory + Eloquent repository implementations
5. **Presentation**: Controller, FormRequests, ApiResource
6. **Wire**: Register bindings in `AppServiceProvider`
7. **Test**: Unit tests for domain + handler; add to contract dataset

See `ARCHITECTURE.md` for deep-dive.
