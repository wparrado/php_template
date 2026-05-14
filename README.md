# PHP Laravel Hexagonal Template

A minimal, production-ready Laravel 11 template demonstrating **Hexagonal Architecture** (Ports & Adapters) with CQRS and DDD patterns.

## Architecture

```
┌─────────────────────────────────────────────────────────┐
│  Presentation  (HTTP adapters)                          │
│  Controllers · Form Requests · API Resources · Middleware│
│                        │ depends on                     │
├─────────────────────────────────────────────────────────┤
│  Application  (use cases — CQRS)                        │
│  Commands · Queries · Handlers · DTOs · Result<T>       │
│                        │ depends on                     │
├─────────────────────────────────────────────────────────┤
│  Domain  ◆ Pure PHP — zero framework dependencies       │
│  Aggregates · Value Objects · Ports · Domain Events     │
│                        △ implemented by                 │
├─────────────────────────────────────────────────────────┤
│  Infrastructure  (secondary adapters)                   │
│  Eloquent Repos · InMemory Repos · Event Publishers     │
│  Clock · AppServiceProvider (composition root)          │
└─────────────────────────────────────────────────────────┘
```

**Dependency rule:** arrows point inward. Domain knows nothing about any other layer.
Infrastructure implements Domain ports; it never leaks into Application or Presentation.

| Layer | Namespace | Responsibility |
|-------|-----------|----------------|
| Domain | `src/Domain/` | Business rules, entities, value objects, ports (interfaces) |
| Application | `src/Application/` | Use-case handlers, commands/queries, DTOs, Result type |
| Infrastructure | `src/Infrastructure/` | Adapters — Eloquent, InMemory, events, clock |
| Presentation | `src/Presentation/` | HTTP controllers, form requests, API resources |

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate          # optional — uses in-memory backend by default
php artisan openapi:generate
php artisan serve
```

Open **http://localhost:8000/api/documentation** for the Swagger UI.

## Running without a database

Set `DB_BACKEND=memory` in `.env` (default). No database setup required.

## Commands

```bash
composer lint              # Laravel Pint auto-format
composer stan              # PHPStan level 9
composer test              # Full Pest suite
composer test:unit         # Unit tests only
composer test:arch         # Architecture boundary tests
composer generate:openapi  # Regenerate OpenAPI JSON
```
