---
description: Designs technical architecture and tasks for a validated requirement
proactive: false
triggers:
  - "design solution"
  - "architect feature"
  - "/requirement-design-solution"
---

# Universal AI Agent Command: Technical Solution Design

Prompt for AI Agents to architect technical solutions (Technical Design & Task Breakdown) from validated requirements documents.

> ⚠️ **NOTE**: This command **MUST NOT BE USED FOR THE CASE TYPE**. Cases are strictly for incident/issue investigations.

---

## 🎯 AI Agent Responsibilities When Designing Solutions

1. Read the requirements document (`*_requirements.md`).
2. Design the architectural mapping adhering strictly to DDD, Hexagonal, and CQRS rules:
   - Identify the target Bounded Context.
   - Design Aggregates, Entities, Value Objects, and Enums (Phase 1).
   - Design Port Interfaces, Models, Migrations, and Mappers (Phase 2).
   - Design Commands, Queries, FormRequests, Actions, ResServices, and Controllers (Phase 3).
3. Generate a technical design document in the same directory: `{feature-name}_design.md`.
4. Generate a master task list document in the same directory: `{feature-name}_tasks.md`.

---

## 📄 Technical Design Document Format (`{feature-name}_design.md`)

```markdown
# Technical Design: [Feature Name]

**Requirement Reference:** [Link to requirements.md]  
**Bounded Context:** `src/[BoundedContextName]/`  

## 1. Domain Model Design (Phase 1)
- **Aggregate Root**: `[EntityName]`
- **Value Objects**: `[IdName]`, `[OtherValueObject]`
- **Enums**: `[StatusEnum]`
- **Domain Events**: `[CreatedEventName]`, `[UpdatedEventName]`

## 2. Persistence & Infrastructure Design (Phase 2)
- **Database Table**: `[table_name]`
- **Repository Interface**: `[EntityName]RepositoryInterface`
- **Hydration Method**: `reconstitute()` on entity

## 3. Application & HTTP Layer Design (Phase 3)
- **Commands (Write - return void)**: `[CreateFeatureCommand]`
- **Queries (Read - return DTO)**: `[GetFeatureQuery]`
- **HTTP Endpoint**: `POST /api/[endpoint]`
- **Request**: `[Feature]Request` (rules + getDto)
- **Action**: `[Feature]Action` (thin, delegates to CommandBus, returns Custom Res)
- **Custom Resource**: `[Feature]Res` implements `JsonSerializable`
```

---

## 📋 Master Task List Document Format (`{feature-name}_tasks.md`)

```markdown
# Task List: [Feature Name]

### Phase 1: Domain Layer
- [ ] Create Value Objects & Enums
- [ ] Create Domain Entity with private constructor & static create()
- [ ] Create Domain Events & status transition methods
- [ ] Create Domain Unit Tests

### Phase 2: Infrastructure Layer
- [ ] Create Repository Interface in Domain
- [ ] Create Database Migration script (including foreign key indexes)
- [ ] Create Eloquent Model
- [ ] Create Mapper (toDomain via reconstitute() and toModel)
- [ ] Create Repository Implementation
- [ ] Register interface bindings in Service Provider
- [ ] Create Repository Integration Tests

### Phase 3: Application & HTTP Layer
- [ ] Create Command & Command Handler (strictly void)
- [ ] Create Query, Query Handler, and DTO/ReadModel
- [ ] Create FormRequest with rules() & getDto()
- [ ] Create Thin Action (≤ 20 lines, returns XxxRes)
- [ ] Create Custom API Resource (XxxRes) & ResService
- [ ] Register endpoint in Controller & routes/api.php
- [ ] Create End-to-End API Feature Tests
```
