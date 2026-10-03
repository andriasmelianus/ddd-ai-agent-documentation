# DDD, Hexagonal Architecture & CQRS — Universal AI Agent Documentation

> Centralized, agnostic repository for documentation, architecture guidelines, code standards, and audit prompts for **AI Agents** (Gemini, Claude, ChatGPT, Cursor, Windsurf, Copilot) and human developers building applications based on **Domain-Driven Design (DDD)**, **Hexagonal Architecture (Ports and Adapters)**, and **CQRS** within the PHP/Laravel ecosystem.

---

## 🚀 Quick Start Guide for AI Agents

If you are an AI Agent assigned to a project adopting these standards, **FOLLOW THIS READING ORDER**:

1. 📌 **[PROJECT_CONTEXT.md](PROJECT_CONTEXT.md)** — Read domain context, active modules, and current project specifications.
2. 🚨 **[02-critical-rules.md](core-architecture/02-critical-rules.md)** — **MANDATORY READING**: Non-negotiable rules that must never be violated.
3. 🏛️ **[01-architecture-overview.md](core-architecture/01-architecture-overview.md)** — Understand layer separation and Bounded Context boundaries.
4. 🔄 **[01-development-workflow.md](development-lifecycle/01-development-workflow.md)** — Apply the 3-phase implementation order (Domain ➔ Infra ➔ Application/HTTP).

---

## 📚 Complete Documentation Index

### 🏛️ 1. Architecture Principles & Core (`core-architecture/`)
*Category: 🔒 Architectural Invariant Principles (Fixed & Static)*
- **[01 - Architecture Overview](core-architecture/01-architecture-overview.md)**: Overview of DDD, Hexagonal Architecture, Bounded Contexts, communication via Bus, and inward dependency rules.
- **[02 - Critical Rules](core-architecture/02-critical-rules.md)**: Top critical rules (Commands return void, N+1 query-free performance, Handlers forbidden from direct DB access, entity invariant protection, IDs as Value Objects).
- **[03 - Application Layer & CQRS](core-architecture/03-application-layer-cqrs.md)**: Queries (Read), Commands (Write), Process Managers (Sagas/Cron), and Domain Event Listeners.
- **[04 - Infrastructure Layer](core-architecture/04-infrastructure-layer.md)**: Repositories, ReadModels (replacing mixed arrays), Table Objects, Hydrators/Mappers using `reconstitute()`, and Eloquent Models.
- **[05 - Code Quality Principles](core-architecture/05-code-quality-principles.md)**: SOLID principles, Stateless Services, Rich Entity vs Domain Service, expressive Enums, and domain naming standards.

### 🌐 2. HTTP Presentation Layer (`presentation-layer/`)
*Category: 🔒 Architectural Invariant Principles (Fixed & Static)*
- **[HTTP Layer Architecture Patterns](presentation-layer/http-layer-patterns.md)**: Complete **Action-Request-Dto-Res** pattern:
  - `FormRequest`: format validation via `rules(): array` and DTO mapping via `getDto(): XxxDto`.
  - `Action`: thin orchestrator (≤ 20 lines), handles only access verification, Bus dispatch, and returning Custom Resource `XxxRes`.
  - `Custom Resource (XxxRes)`: implements `\JsonSerializable` (not Laravel's `JsonResource`).
  - `Controller`: converts Resource to `response()->json($resource, $status)`.

### 🔄 3. Feature Lifecycle & Requirements (`development-lifecycle/`)
*Category: 🔄 Living Documents (Can & Must Be Updated Periodically)*
- **[01 - Development Workflow](development-lifecycle/01-development-workflow.md)**: Mandatory 3-Phase feature implementation order (Phase 1: Domain ➔ Phase 2: Infra ➔ Phase 3: App & HTTP) along with verification checklists.
- **[02 - Requirements Engineering](development-lifecycle/02-requirements-engineering.md)**: Requirements analysis guide ("Analysis Informs, Never Blocks"), CRUD checks, state/lifecycle analysis, and collateral impact mitigation.
- **[03 - Requirement Templates](development-lifecycle/03-requirement-template.md)**: Standard templates for **Epic**, **Feature**, **Hotfix**, and **Case (Investigation Only)**.

### 🤖 4. AI Agent Commands & Audits (`agent-commands-and-audits/`)
*Category: 🛠️ Agnostic Audit Prompts (Executable by any Agent)*
- **[audit-architecture.md](agent-commands-and-audits/audit-architecture.md)**: Comprehensive audit prompt for DDD, Hexagonal, and CQRS compliance.
- **[validate-http-layer.md](agent-commands-and-audits/validate-http-layer.md)**: Audit prompt specifically for the HTTP Layer (thin Actions, Request getDto, Custom Res).
- **[analyze-performance.md](agent-commands-and-audits/analyze-performance.md)**: Audit prompt for N+1 queries, eager loading, and missing database indexes.
- **[requirement-validate.md](agent-commands-and-audits/requirement-validate.md)**: Prompt to validate requirement document completeness.
- **[requirement-design-solution.md](agent-commands-and-audits/requirement-design-solution.md)**: Prompt to design technical architectural solutions from requirements.

### ⚙️ 5. Governance & Agent Compatibility
- **[PROJECT_CONTEXT.md](PROJECT_CONTEXT.md)**: Profile document and specific context for the active project (*living document*).
- **[DOCUMENTATION_GOVERNANCE.md](DOCUMENTATION_GOVERNANCE.md)**: Document update policy (distinction between architectural invariant principles vs. periodically updated living documents).
- **[AGENT_COMPATIBILITY_GUIDE.md](AGENT_COMPATIBILITY_GUIDE.md)**: Configuration guide for Claude Code, Gemini/Antigravity, Cursor, Windsurf, and ChatGPT.

---

## 🚨 Summary of 7 Critical Rules (Golden Rules)

```
1. STRICT CQRS      : Commands ALWAYS return void (IDs generated BEFORE dispatch).
2. NO N+1 QUERIES   : Never execute queries inside loops (collect IDs, use WHERE IN).
3. NO DB IN HANDLERS: Application Handlers are FORBIDDEN from touching DB:: directly (always via Repositories).
4. IDS AS VALUE OBJS: ID parameters on domain interfaces are Value Objects, NOT raw strings.
5. INVARIANT ENTITY : Entities use private constructors; use create() and reconstitute() (NO Reflection).
6. THIN ACTIONS     : Actions ≤ 20 lines, no business logic, ALWAYS return Custom Resource (XxxRes).
7. TYPED REQUESTS   : Requests have rules() for HTTP format and getDto() for strongly-typed DTOs.
```

---

## 🏛️ Document Evolution Policy

According to [DOCUMENTATION_GOVERNANCE.md](DOCUMENTATION_GOVERNANCE.md):
- **Architectural Principles** (`core-architecture/*`, `presentation-layer/*`): Invariant and never modified without architect team consensus.
- **Living Documents** (`PROJECT_CONTEXT.md`, task lists, requirements, audit reports): **Can and must be updated periodically** by AI Agents or developers as the project evolves.
