# PHP Laravel Hexagonal Template

A minimal, production-ready Laravel 11 template demonstrating **Hexagonal Architecture** (Ports & Adapters) with CQRS and DDD patterns.

## Architecture

```mermaid
graph TD
    subgraph Presentation["🌐 Presentation (HTTP Adapters)"]
        C[Controllers]
        FR[Form Requests]
        AR[API Resources]
        MW[Middleware]
    end

    subgraph Application["⚙️ Application (Use Cases — CQRS)"]
        CMD[Commands]
        QRY[Queries]
        HDL[Handlers]
        DTO[DTOs]
        RES[Result&lt;T&gt;]
    end

    subgraph Domain["🧠 Domain (Core — Pure PHP)"]
        AGG[Aggregates]
        VO[Value Objects]
        PRT[Ports / Interfaces]
        EVT[Domain Events]
    end

    subgraph Infrastructure["🔧 Infrastructure (Secondary Adapters)"]
        ELQ[Eloquent Repositories]
        MEM[InMemory Repositories]
        CLK[Clock]
        SP[AppServiceProvider]
    end

    Presentation -->|depends on| Application
    Application -->|depends on| Domain
    Infrastructure -->|implements| Domain

    style Domain fill:#fef9c3,stroke:#ca8a04,color:#000
    style Application fill:#dbeafe,stroke:#2563eb,color:#000
    style Presentation fill:#dcfce7,stroke:#16a34a,color:#000
    style Infrastructure fill:#fce7f3,stroke:#db2777,color:#000
```

> **Dependency rule:** arrows point inward only. `Domain` has zero framework dependencies — it knows nothing about Laravel, Eloquent, or HTTP. `Infrastructure` implements the `Domain` ports; it never leaks into `Application` or `Presentation`.

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
