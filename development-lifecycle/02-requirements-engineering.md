# 02 - Requirements Engineering & Analysis Guide

Guide for AI Agents and developers in analyzing, validating, and structuring requirements documents prior to technical design and coding phases.

---

## 💡 Core Philosophy: "Analysis Informs, Never Blocks"

**THE USER ALWAYS HOLDS THE FINAL DECISION.**

| Principle | Practical Meaning |
|---|---|
| **Analysis is informative** | Highlights risks, logical loopholes, and collateral impact — **NOT** blocking work. |
| **User decides** | If the user says "proceed / continue", we execute their instructions immediately. |
| **Not rigid bureaucracy** | Mature definitions are invaluable to prevent patch-upon-patch development (*parches sobre parches*), yet they must never stifle delivery velocity. |

> 🤖 **AI Response Guidelines**:
> - ❌ NEVER say: *"Cannot proceed until X is provided."*
> - ✅ ALWAYS say: *"Information X is currently undefined or carries risk Y. Would you like to define it now or proceed directly with implementation?"*

---

## 🗂️ 4 Requirement Categories & Working Directories

Requirements documents are stored under `docs/working_docs/`:

```
docs/working_docs/
├── epics/           # Major initiatives with comprehensive business justification
├── features/        # Specific features subordinate to an Epic
├── hotfixes/        # Emergency bug fixes in production environments
└── cases/           # Technical incident investigations (INVESTIGATION ONLY, NO CODE)
```

### Characteristics of Each Type:

| Type | Purpose | Business Justification | Analysis Depth | Coding Involved? |
|---|---|---|---|---|
| **Epic** | Large-scale initiative | Mandatory (KPIs, ROI) | Full (CRUD, State, Slicing) | Yes (incremental) |
| **Feature** | Component of an Epic | Reference to Parent Epic | Feature-specific | Yes |
| **Hotfix** | Emergency repair | Bug issue = Justification | Problem-focused + Rollback | Yes (direct) |
| **Case** | Incident / bug analysis | Not applicable | Root Cause Investigation | **NONE** |

---

## 🔍 Step-by-Step Requirements Analysis Process

### Step 0: Detect Requirement Type
Detect the type based on document path (`/epics/`, `/features/`, `/hotfixes/`, `/cases/`).

---

### Step 1 & 2: Entity Identification & CRUD Checks
For every key business entity involved, verify the completeness of its CRUD lifecycle:
- **Create**: How is this entity instantiated?
- **Read / View**: How are entity details inspected?
- **Update**: Which data points can be modified after creation?
- **Delete**: How is it deleted? (Hard delete vs Soft delete).
- **List / Search**: How is this entity listed, searched, or filtered?

---

### Step 3: Status & State Machine Analysis (MANDATORY)
Almost every domain entity possesses a lifecycle. The AI must verify:
1. **Initial Status**: What is the initial status when an entity is first created? (e.g., `DRAFT`, `PENDING`).
2. **All Possible States**: What are all valid states?
3. **Valid Transitions**: Which transitions are legal? (e.g., from `PENDING` -> `CONFIRMED`, but forbidden from `CANCELLED` -> `CONFIRMED`).
4. **Triggers & Conditions**: What triggers each transition? (User action, automatic event, expiration timeout).
5. **Side Effects**: Does the transition trigger domain events, notifications, or mutate other entities?

---

### Step 4 & 5: Use Case Patterns & Inverse Operations
Whenever a business action is proposed, inspect whether its counterpart action is necessary:

| Requested Action | Inspect Counterpart / Related Actions |
|---|---|
| Create Booking | Cancel, Reschedule, Confirm |
| Add Contact | Remove Contact, Update Contact |
| Enable Feature | Disable Feature |
| Approve | Reject, Request Revision |
| Soft Delete | Restore |

---

### Step 6: User Journey & Error Handling
- What are the prerequisites (*preconditions*) before the action takes place?
- What are the consequences (*consequences*) upon successful execution?
- **Error Recovery**: What happens if the user inputs incorrect data?
- **Undo / Change Mind**: What if the user changes their mind after submitting?

---

### Step 7: Collateral Impact Analysis
New features rarely exist in isolation. The AI must evaluate impacts on existing operational systems:
1. **Breaking Changes**: Does this change break existing API contracts or database structures?
2. **Behavioral Changes**: Will existing calculation logic yield altered outputs?
3. **Data Impact**: Does existing database data require a data migration script?
4. **UI & API Impact**: Which screens or endpoints are affected?
5. **Performance Impact**: Does any new validation introduce heavy queries?

---

### Step 8: Slicing Strategy (Breaking Down Large Features)
If a requirement is too extensive (> 7 use cases, > 3 entities, or spans multiple weeks), it **MUST be partitioned into multiple slices/phases**:
- **Slice 1 (MVP)**: Most critical flow providing immediate standalone value (e.g., Create + View).
- **Slice 2**: Secondary flows (Edit + Cancel).
- **Slice 3**: Polishing features (Notifications + Reporting).
- **Out of Scope Rule**: Do not relegate essential functionality (such as validation and basic error handling) to "Out of Scope".

---

## 🚫 Anti-Patterns to Flag

| Anti-Pattern | Bad Example | Good Corrected Example |
|---|---|---|
| **Ambiguous Language** | *"System must be fast"* | *"API response time < 200ms at p95"* |
| **Solution as Requirement** | *"Use Redis for caching"* | *"Frequently accessed data must load in < 50ms"* |
| **Partial Lifecycle** | *"Just build Add Order screen"* | *"Define full lifecycle: Add, View, Cancel, Complete"* |
| **Ignoring Collateral Impact** | *"Add discount column to orders"* | *"Adding discount -> update invoice, tax, financial reporting"* |
| **Flawed CRUD Slicing** | *"Phase 1: Create Order. Phase 2: View Order"* | *"Phase 1 must include Create and View to provide viable value"* |
| **Subjective Justification** | *"Many users want this feature"* | *"15 customer support tickets requested this (total contract value €20k)"* |
