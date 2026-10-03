---
description: Audits DDD and Hexagonal architecture compliance
proactive: true
triggers:
  - "violating"
  - "does not comply"
  - "incorrect architecture"
  - "review code"
  - "audit"
  - "validate architecture"
---

# Universal AI Agent Audit: DDD & Hexagonal Architecture Compliance

Prompts and instructions for AI Agents (Gemini, Claude, ChatGPT, Cursor, etc.) to audit code compliance against **Domain-Driven Design (DDD)**, **Hexagonal Architecture**, and **CQRS** rules.

---

## 🎯 Audit Instructions for AI Agents

When requested to conduct an architectural audit:
1. Scan the entire codebase under `Apps/` and `src/`.
2. Analyze code against the 8 critical compliance areas below.
3. Categorize findings by severity: **CRITICAL**, **HIGH**, and **MEDIUM**.
4. Save the audit findings report to a markdown file at: `docs/Reports/YYYY-MM-DD-audit-architecture.md`.

---

## 🔍 8 Mandatory Compliance Areas to Inspect:

### 1. HTTP Layer — Actions (CRITICAL)
Location: `Apps/Api/**/*/Action.php`
- ❌ **Violations**:
  - Calling `DB::table()`, `DB::statement()`, or Query Builder.
  - Calling Eloquent Models (`Model::find()`, `Model::where()`).
  - Presence of loops (`foreach`, `array_map`) for business logic or domain data transformation.
  - Containing business logic, price/discount calculations, or domain validation.
  - Returning `JsonResponse` directly (should return Custom Resource `XxxRes`).
  - Returning internal DTOs or raw arrays.
  - Method length > 20 lines.
- ✅ **Standard**:
  - Performs solely: access verification (JWT), command/query dispatch, and returning `XxxRes` via `ResService`.

### 2. HTTP Layer — Requests (CRITICAL)
Location: `Apps/Api/**/*/Request.php`
- ❌ **Violations**:
  - Missing `getDto(): XxxDto` method.
  - Passing raw request data or untyped data to Actions/Handlers.
- ✅ **Standard**:
  - May contain `rules(): array` for basic HTTP format validation.
  - Must provide `getDto(): XxxDto` to map inputs into a strongly-typed DTO.

### 3. Application Layer — Handlers (CRITICAL)
Location: `src/**/Application/**/Handler.php`
- ❌ **Violations**:
  - Using direct `DB::` calls in any form.
  - Using Eloquent Models directly.
- ✅ **Standard**:
  - Always inject `*RepositoryInterface` from the Domain.

### 4. CQRS — Commands (CRITICAL)
Location: `src/**/Application/Commands/**/`
- ❌ **Violations**:
  - Command Handlers returning any value other than `void` (e.g., `return $id;` or returning an Entity).
- ✅ **Standard**:
  - Handler return type is strictly `void`.
  - IDs are generated before dispatching the command and passed in as command parameters.

### 5. Entity Invariants & Construction Pattern (CRITICAL)
Location: `src/**/Domain/Entities/*.php`
- ❌ **Violations**:
  - `public` constructors without invariant encapsulation.
  - Presence of public setters (`setStatus()`, `setItems()`).
  - Mappers utilizing `ReflectionClass::newInstanceWithoutConstructor()`.
- ✅ **Standard**:
  - `private` constructor.
  - Static method `create(...)` for new data (with Domain Events).
  - Static method `reconstitute(...)` for database hydration (without Domain Events).

### 6. Database Performance — Queries in Loops (CRITICAL)
Location: All files across `src/` and `Apps/`
- ❌ **Violations**:
  - Repository invocations or database queries executed inside loops (`foreach`, `while`).
- ✅ **Standard**:
  - Collect all IDs upfront, then execute a single query using an `IN (...)` clause.

### 7. Value Objects — ID Typing (HIGH)
Location: `src/**/Domain/Repositories/*Interface.php`
- ❌ **Violations**:
  - ID parameters typed with primitive `string` or `int`.
- ✅ **Standard**:
  - ID parameters must use typed Value Objects (e.g., `BookingId $id`, `ClientId $id`).

### 8. Bounded Context Boundaries (HIGH)
Location: `src/**`
- ❌ **Violations**:
  - Entities or Repositories directly accessed across Bounded Contexts without going through QueryBus / CommandBus.
  - Direct SQL JOINs across tables belonging to different Bounded Contexts.

---

## 📄 Audit Report Format (`docs/Reports/YYYY-MM-DD-audit-architecture.md`)

```markdown
# DDD and Hexagonal Architecture Audit Report

**Date:** YYYY-MM-DD  
**Auditor Agent:** [AI Agent Name]  
**Status:** Completed  

## 🚨 CRITICAL VIOLATIONS

### 1. [File Name]:[Line Number]
- **Category**: [HTTP Action / Direct DB / Command Return]
- **Violating Code Snippet**:
  ```php
  // Violating code
  ```
- **Issue Explanation**: [Why it violates architectural rules]
- **Remediation Solution**:
  ```php
  // Recommended remediation code
  ```

## ⚠️ HIGH & MEDIUM VIOLATIONS
[List of other findings]

## 📊 Statistics Summary
- Critical: X findings
- High: X findings
- Medium: X findings
- Total Files Inspected: X files

## 🎯 Action Priorities
1. Refactor Actions and Handlers touching direct DB calls.
2. Fix Command Handler signatures to be strictly void.
3. Refactor entity hydration to use `reconstitute()`.
```
