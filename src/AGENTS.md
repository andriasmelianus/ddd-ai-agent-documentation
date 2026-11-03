# AI Agent Guidelines & Operating Instructions (AGENTS.md)

> **MANDATORY INSTRUCTION FOR ALL AI AGENTS:**  
> Before planning, modifying, generating code, or responding to user development requests, you **MUST** read the project architecture documentation starting at [`docs/README.md`](../docs/README.md).

---

## 🚨 1. Mandatory Bootstrap Protocol (Read First)

Whenever you (the AI Agent) are invoked on any task within this codebase, execute the following initialization steps in order:

1. **Read Entrypoint**:  
   Read [`docs/README.md`](../docs/README.md) (or `docs/README.md` from the project root) to understand the overarching system structure.
2. **Review Project Context**:  
   Read [`docs/PROJECT_CONTEXT.md`](../docs/PROJECT_CONTEXT.md) to understand current domain context, active bounded contexts, and project requirements.
3. **Internalize Critical Invariant Rules (MANDATORY)**:  
   Read [`docs/core-architecture/02-critical-rules.md`](../docs/core-architecture/02-critical-rules.md). **These rules are non-negotiable and must never be violated.**
4. **Inspect Architecture & Workflow**:  
   - [`docs/core-architecture/01-architecture-overview.md`](../docs/core-architecture/01-architecture-overview.md) (Hexagonal Architecture & CQRS layers).
   - [`docs/development-lifecycle/01-development-workflow.md`](../docs/development-lifecycle/01-development-workflow.md) (3-Phase development cycle).

---

## 🏛️ 2. Core Architectural Principles (Quick Summary)

All code produced in this repository must strictly adhere to **Domain-Driven Design (DDD)**, **Hexagonal Architecture (Ports & Adapters)**, and **CQRS**:

```
+---------------------------------------------------------------------------------+
|                                8 GOLDEN RULES                                   |
+---------------------------------------------------------------------------------+
| 1. STRICT CQRS      : Commands ALWAYS return void. Generate IDs BEFORE dispatch.|
| 2. NO N+1 QUERIES   : Never execute SQL queries inside loops (use WHERE IN).   |
| 3. NO DB IN HANDLERS: Handlers NEVER access DB:: directly (use Repositories).   |
| 4. IDS AS VALUE OBJS: Entity & Aggregate IDs are Value Objects, NOT raw strings.|
| 5. INVARIANT ENTITY : Entities have private constructors; use create() and      |
|                       reconstitute() (strictly NO reflection).                  |
| 6. THIN ACTIONS     : Actions <= 20 lines, zero business logic, ALWAYS return   |
|                       Custom Resource (XxxRes implements \JsonSerializable).   |
| 7. TYPED REQUESTS   : Requests define rules() for format validation AND         |
|                       getDto() returning strongly-typed DTOs.                   |
| 8. CQRS PERSISTENCE : Repositories for Write model (*RepositoryInterface),      |
|                       Queries for Read model (*QueryInterface).                 |
+---------------------------------------------------------------------------------+
```

---

## 🔄 3. Feature Development Lifecycle (3-Phase Order)

When creating or modifying features, always implement code strictly in this sequence:

### Phase 1: Domain Layer (Inward / Core)
- Define Domain Events and Value Objects (including Entity IDs).
- Implement Entities/Aggregates with private constructors and business invariant validation.
- Define Repository Interfaces (in `Domain/Model/*/` or `Domain/Repositories/`).

### Phase 2: Infrastructure Layer (Adapters)
- Implement Repositories implementing Domain Repository Interfaces.
- Create Read Models, Table Objects, and Hydrators using `reconstitute()`.
- Map database tables using Eloquent models as persistence storage only.

### Phase 3: Application & Presentation Layer
- Implement Commands, Command Handlers, Queries, and Query Handlers.
- Implement HTTP Requests (`FormRequest` with `rules()` and `getDto()`).
- Implement Thin Actions (≤ 20 lines) and Custom Resources (`XxxRes`).
- Implement Controllers returning `response()->json($resource, $status)`.

---

## 🛠️ 4. Self-Audit & Verification Commands

Before completing your turn or marking any task done, run self-audits against the templates in `docs/agent-commands-and-audits/`:

- **Architecture Compliance**: Refer to [`docs/agent-commands-and-audits/audit-architecture.md`](../docs/agent-commands-and-audits/audit-architecture.md).
- **HTTP Layer Patterns**: Refer to [`docs/agent-commands-and-audits/validate-http-layer.md`](../docs/agent-commands-and-audits/validate-http-layer.md).
- **Performance & N+1 Audits**: Refer to [`docs/agent-commands-and-audits/analyze-performance.md`](../docs/agent-commands-and-audits/analyze-performance.md).

---

## 🧭 5. Documentation Path Reference

If reading via relative or absolute filesystem paths:

- **Relative from `src/`**: `../docs/README.md`
- **Relative from project root**: `docs/README.md`
- **Absolute path**: `/home/it/projects/shared/ddd-ai-agent-documentation/docs/README.md`
