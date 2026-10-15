# Documentation Governance & Evolution Policy

This document governs the maintenance, update, and lifecycle rules for all documents within this documentation repository.

---

## 🏛️ Core Principle: Separation of Architectural Invariants vs Living Documents

The documentation is divided into two categories with different update rules:

```
┌─────────────────────────────────────────────────────────────────┐
│                      SYSTEM DOCUMENTATION                       │
├────────────────────────────────┬────────────────────────────────┤
│ 1. Architectural Principles    │ 2. Living Documents            │
│    (Architectural Invariants)  │                                │
│                                │                                │
│ 🔒 INVARIANT / STATIC          │ 🔄 CAN BE UPDATED PERIODICALLY │
│ - DDD Core Rules               │ - Project Context              │
│ - Hexagonal Layering           │ - Requirements (Epic/Feature)  │
│ - Strict CQRS (void commands)  │ - Task Lists & Roadmaps        │
│ - Invariant Enforcements       │ - Bug & Incident Cases         │
│ - Performance & DB Rules       │ - Audit Reports                │
│                                │ - Helper Catalogs & Notes      │
└────────────────────────────────┴────────────────────────────────┘
```

---

## 1. Architectural Principles (Invariant / Static)

Documents in this category include:
- `core-architecture/*` (Architecture Overview, Critical Rules, CQRS, Infrastructure, Code Quality)
- `presentation-layer/*` (HTTP Layer Patterns, Thin Actions, Custom Resources)
- `development-lifecycle/01-development-workflow.md` (3-Phase Implementation Order)

### Rules:
1. **Invariant Rules**: These principles are fixed and must not be altered arbitrarily during routine feature development.
2. **AI Agent Protection**: AI agents are forbidden from loosening or modifying architectural principles without explicit approval from the team/lead architect (e.g., forbidden to allow queries in loops, forbidden to return data from Commands, forbidden to access `DB::` directly in Handlers).
3. **Change Procedure**: Modifications to architectural principles can only be made through a formal **Architectural Decision Record (ADR)** agreed upon by domain experts and tech leads.

---

## 2. Living Documents (Can & Must Be Updated Periodically)

Documents in this category include:
- `PROJECT_CONTEXT.md` (Active project profile, modules, configurations, and status)
- `development-lifecycle/02-requirements-engineering.md` & templates
- Working documents (`docs/working_docs/epics/*`, `features/*`, `hotfixes/*`, `cases/*`)
- Reporting documents (`docs/Reports/YYYY-MM-DD-*.md`)
- Per-feature task lists (`[feature]_tasks.md`)

### Periodic Update Rules:
1. **Periodic Synchronization**: Living documents **must and can be updated periodically** as sprints progress, new modules are introduced, dependencies change, or milestones are achieved.
2. **AI Agent Authority**:
   - AI agents **are authorized and encouraged** to update `PROJECT_CONTEXT.md` upon detecting environment changes, new bounded contexts, new schema migrations, or new endpoints.
   - AI agents must update task list statuses (checklist `[x]`) whenever a sub-task is completed.
   - AI agents must generate or update audit/report documents when executing architectural validation or audit processes.
3. **Historical Clarity**: Every update to living documents must provide context or rationale for the change (e.g., addition of a new Bounded Context, PHP/Laravel version upgrades, or incident investigation findings).

---

## 📋 AI Agent Checklist Before Modifying Documents

Before editing any document:

- [ ] **Identify Category**: Is this document an *Architectural Principle* or a *Living Document*?
- [ ] **If Architectural Principle**:
  - Did the user explicitly request a change to architectural rules?
  - If NO, preserve the rules and do not alter foundational principles.
- [ ] **If Living Document**:
  - Update with the latest data (project status, configurations, bounded contexts, etc.).
  - Ensure formatting remains clean and consistent with standard templates.
  - Update the last updated timestamp.
