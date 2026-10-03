# 01 - Architecture Overview

Comprehensive guide to applying **Domain-Driven Design (DDD)**, **Hexagonal Architecture (Ports and Adapters)**, and Layer Boundaries.

---

## 🏛️ Foundational Architectural Principles

This system's architecture is built on 4 main pillars:

1. **Domain-Centric (Framework-Agnostic)**: Core business logic (Domain) is placed inside the `src/` directory, entirely free of dependencies on the Laravel framework, databases, or HTTP protocols.
2. **Hexagonal Architecture**: Strict separation between business logic and the external world using Ports (Interfaces) in the domain and Adapters (Implementations) in infrastructure/apps.
3. **CQRS (Command Query Responsibility Segregation)**: Total separation between write operations (Command -> void) and read operations (Query -> DTO/ReadModel).
4. **Thin Presentation Layer**: The HTTP layer (`features/Api/`) functions solely as a thin orchestrator that validates syntax, maps to DTOs, delegates to the Bus, and formats output.

---

## 📐 Layer Dependency Flow (Layer Boundaries)

Dependencies always point inwards (towards the Domain Layer). The Domain never depends on any other layer.

```
       ┌────────────────────────────────────────────────────────┐
       │               HTTP / Apps Layer (features/)                │
       │  (Controllers, FormRequests, Actions, API Resources)   │
       └───────────────────────────┬────────────────────────────┘
                                   │ dispatches
                                   ▼
       ┌────────────────────────────────────────────────────────┐
       │         Application Layer (src/*/Application/)         │
       │    (Commands, Queries, Handlers, Process Managers)     │
       └───────────────────────────┬────────────────────────────┘
                                   │ uses
                                   ▼
       ┌────────────────────────────────────────────────────────┐
       │            Domain Layer (src/*/Domain/) ◄──────────────┤
       │ (Entities, Value Objects, Enums, Domain Events, Ports) │
       └───────────────────────────▲────────────────────────────┘
                                   │ implemented by
       ┌───────────────────────────┴────────────────────────────┐
       │       Infrastructure Layer (src/*/Infrastructure/)     │
       │      (Repositories, Eloquent Models, Hydrators, DB)    │
       └────────────────────────────────────────────────────────┘
```

### Summary of Layer Responsibilities:

| Layer | Directory | Primary Responsibility | Permitted Dependencies |
|---|---|---|---|
| **Domain** | `src/{BC}/Domain/` | Business rules, invariants, entities, value objects, ports (interfaces). | **NONE** (Pure PHP, framework-agnostic). |
| **Application** | `src/{BC}/Application/` | Use case orchestration, Commands, Queries, Handlers, Process Managers. | Depends on Domain. |
| **Infrastructure** | `src/{BC}/Infrastructure/` | Ports implementation, MySQL/Postgres DB access, Eloquent, third-party SDKs. | Implements Domain interfaces, calls Laravel/DB. |
| **HTTP (Apps)** | `features/Api/` | HTTP routing, input syntax validation, auth access checks, DTO mapping, JSON serialization. | Calls Application Bus (CommandBus/QueryBus), Domain DTOs/Value Objects. |

---

## 🗂️ Bounded Context Directory Structure

Every Bounded Context inside `src/` follows a uniform internal structure:

```
src/
├── {BoundedContextName}/
│   ├── Domain/
│   │   ├── Entities/          # Rich business entities (invariants protected)
│   │   ├── ValueObjects/      # Immutable value objects (Ids, Money, Email, etc.)
│   │   ├── Enums/             # PHP 8.1+ Enums with lightweight business helpers
│   │   ├── Events/            # Domain Events (recording past facts)
│   │   ├── Exceptions/        # Domain-specific exceptions (not HTTP exceptions)
│   │   ├── ReadModels/        # Optimized read DTOs for repository queries
│   │   ├── Services/          # Domain services (multi-entity logic)
│   │   └── Repositories/      # Repository ports/interfaces
│   │
│   ├── Application/
│   │   ├── Commands/          # Write operations (Command + Handler -> void)
│   │   ├── Queries/           # Read operations (Query + Handler -> DTO/ReadModel)
│   │   ├── ProcessManagers/   # Long-running process orchestration / Saga / Cron
│   │   ├── Listeners/         # Event listeners for domain events
│   │   └── Projections/       # Read model projection generators if required
│   │
│   └── Infrastructure/
│       ├── Persistence/       # Repository implementations & Eloquent Models
│       ├── Hydrators/         # Mappers/Hydrators between DB Models and Domain Entities
│       ├── Gateways/          # External system adapters
│       └── Listeners/         # Infrastructure event listeners (queue, logging)
```

---

## 🌐 Transversal (Cross-Cutting) Bounded Contexts

Functionality shared across multiple Bounded Contexts is placed in `src/Shared/` following these criteria:

1. **Used across multiple Bounded Contexts** (not specific to a single domain).
2. **Provides utility or infrastructure capabilities** (not core business logic).
3. **Implements generic capabilities** (e.g., Saga Engine, Generic Audit Logging, Notification Dispatcher, Search Engine).

```
src/Shared/
├── Framework/              # Base classes (BaseEntity, Ulid, Bus interfaces)
├── Saga/                   # Long-running process orchestration
└── Audit/                  # Generic audit logging
```

---

## 🔄 Inter-Bounded Context Communication

### 1. Primary Method: QueryBus and CommandBus
Communication between Bounded Contexts **MUST** go through the Application Bus:

```php
// From Bounded Context A, fetching data from Bounded Context B:
$client = $this->queryBus->query(new GetClientByIdQuery($clientId));

// From Bounded Context A, triggering an action in Bounded Context B:
$this->commandBus->dispatch(new CreateInvoiceCommand($invoiceData));
```

### 2. Cross-BC Data Joining Rules (Prevent Database Coupling)
Direct SQL `JOIN`s across tables belonging to different Bounded Contexts are forbidden.
Perform data joins at the Application service / PHP level:

```php
// 1. Fetch invoices from Billing BC
$invoices = $this->invoiceQuery->findByRestaurant($restaurantId);

// 2. Collect Client IDs
$clientIds = array_map(static fn($inv) => $inv->clientId, $invoices);

// 3. Fetch client data in 1 query via IN clause to Client BC
$clients = $this->clientQuery->findByIds($clientIds);

// 4. Join data in PHP memory
foreach ($invoices as $invoice) {
    $invoice->client = $clients[$invoice->clientId] ?? null;
}
```

### 3. Data Boundary Permissions Across Bounded Contexts

| Component | Allowed Outside BC? | Rationale |
|---|---|---|
| **Entities** | ❌ **FORBIDDEN** | Entities have invariants and state that must only be managed by their originating BC. Use DTOs / ReadModels. |
| **Repositories** | ❌ **FORBIDDEN** | Internal data access must not be exposed to other BCs. |
| **Domain Services** | ❌ **FORBIDDEN** | Internal business logic must remain private. |
| **Simple Value Objects** | ✅ **ALLOWED** | Simple, stable value objects like `ClientId`, `RestaurantId`, `Money`. |
| **Enums** | ✅ **ALLOWED** | Common status value enumerations such as `BookingStatus`, `PaymentStatus`. |
| **ReadModels / DTOs** | ✅ **ALLOWED** | Read-only data transfer objects without domain behaviors. |

---

## 👥 Role of Domain Experts Before Modifying BC Code

Every Bounded Context has owners/experts (Product Owner & Tech Lead).
Rules before touching or modifying another Bounded Context:
1. Discuss the approach and its impact on the existing domain model.
2. Avoid taking shortcuts directly into tables belonging to other modules.
3. Always design changes using the [Aggregate Design Canvas](https://github.com/ddd-crew/aggregate-design-canvas) when introducing a new Aggregate.

---

## 🔗 Related Documentation References
- [02-critical-rules.md](02-critical-rules.md) - Critical rules that must not be violated.
- [03-application-layer-cqrs.md](03-application-layer-cqrs.md) - CQRS, Command, Query, and Event implementation details.
- [04-infrastructure-layer.md](04-infrastructure-layer.md) - Repository, ReadModel, Mapper, and Eloquent details.
- [presentation-layer/http-layer-patterns.md](../presentation-layer/http-layer-patterns.md) - HTTP Request, Action, DTO, and Resource patterns.
