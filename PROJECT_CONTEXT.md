# Project Context & Living Profile

> 🔄 **LIVING DOCUMENT**: This document is NOT a static architectural principle. This document **must and can be updated periodically** by developers or AI Agents as the project evolves (e.g., adding a new Bounded Context, changing runtime configuration, adding external integrations, or updating roadmaps).

---

## 📌 Project Summary

| Attribute | Value / Description |
|---|---|
| **Project Name** | *[Fill in project name, e.g., Restaurant Booking System / CRM Service]* |
| **Brief Description** | *[1-3 sentence explanation of the primary business domain of this project]* |
| **Repository / Path** | *[Local path or project repository URL]* |
| **Current Status** | Development / Staging / Production |
| **Last Updated** | YYYY-MM-DD |

---

## ⚙️ Environment & Tech Stack

| Component | Specification / Configuration | Special Notes |
|---|---|---|
| **PHP Version** | `PHP 8.2+` (or `PHP 8.4`) | Strict types enforced (`declare(strict_types=1);`) |
| **Framework** | `Laravel 11+` / `Laravel 12` | Domain isolated in `/src`, Laravel solely acts as infrastructure |
| **Database** | MySQL / PostgreSQL | Large database, prevent N+1, foreign key index columns required |
| **Runtime / Container** | Docker (Docker Compose) | Artisan & composer execution required inside container |
| **Bus & Messaging** | In-Memory / RabbitMQ / Redis | CQRS CommandBus & QueryBus |
| **Static Analysis** | PHPStan (Level 8/9 / Max) | All properties strictly typed, PHPDoc for generic types |
| **Testing** | PHPUnit / Pest | Unit test domain (in-memory), Integration test repo (DB test) |

---

## 🗺️ Bounded Contexts & Module Map

List of active Bounded Contexts in this project (under the `src/` directory):

```
src/
├── [BoundedContextA]/        # Description of domain A
│   ├── Domain/
│   ├── Application/
│   └── Infrastructure/
├── [BoundedContextB]/        # Description of domain B
│   ├── Domain/
│   ├── Application/
│   └── Infrastructure/
└── Shared/                   # Transversal/Cross-cutting capabilities
    ├── Framework/            # BaseEntity, Ulid, Bus interfaces
    └── [CrossCuttingModule]/ # Saga, Audit, Notification (if any)
```

### Bounded Context Details:

### 1. `[Bounded Context 1 Name]`
- **Domain Responsibility**: *[What domain problem is solved]*
- **Key Aggregates & Entities**: `[Entity1]`, `[Entity2]`
- **Domain Expert / PIC**: *[Developer / tech lead name]*
- **Module Status**: Stable / Active Development / Planned

### 2. `[Bounded Context 2 Name]`
- **Domain Responsibility**: *[What domain problem is solved]*
- **Key Aggregates & Entities**: `[Entity3]`, `[Entity4]`
- **Domain Expert / PIC**: *[Developer / tech lead name]*
- **Module Status**: Stable / Active Development / Planned

---

## 🌐 External System Integrations & Identification

If the project integrates with external systems (External Providers / Legacy Systems), record details here:

| External System | Integration Purpose | Identification Type (`AppEnum` / External ID) | Adapter / Client Location |
|---|---|---|---|
| *[Example: Payment Gateway]* | Payment transactions | Uses `AppComposedId` / External reference | `src/[BC]/Infrastructure/Gateways/` |
| *[Example: CRM / Legacy DB]* | User data synchronization | Internal ULID + External AppComposedId | `src/[BC]/Infrastructure/Adapters/` |

---

## 🚀 Standard Development Commands (Cheat Sheet)

```bash
# Execute testing in Docker
docker compose exec php php artisan test

# Execute PHPStan
docker compose exec php ./vendor/bin/phpstan analyse

# Database migration
docker compose exec php php artisan migrate

# Code formatting / Code style
docker compose exec php ./vendor/bin/pint
```

---

## 📈 Active Roadmap & Milestones

- [ ] **Milestone 1**: *[Target description]*
- [ ] **Milestone 2**: *[Target description]*
- [ ] **Milestone 3**: *[Target description]*

---

## 📝 Special Notes & Team Decisions (Project Overrides)

This section records team-specific agreements applicable to this project:
- *Note 1: [e.g., Default pagination rule is cursor pagination for feed endpoints]*
- *Note 2: [e.g., Soft delete is required for all financial transactions]*
- *Note 3: [e.g., Internal default ID format is 26-character ULID]*

---

## 🤖 Instructions for AI Agents Working on This Project:
1. Read this document first before starting any new task to understand the domain context, module names, and active environment.
2. Whenever you introduce a new Bounded Context or add an external integration, **update this document automatically**.
3. Ensure domain class names and namespaces align with the Bounded Context list above.
