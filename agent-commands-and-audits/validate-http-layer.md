---
description: Validates HTTP Layer (Actions, Requests, DTOs, and Resources)
proactive: true
triggers:
  - "create action"
  - "new action"
  - "create request"
  - "new request"
  - "validate http"
  - "review http layer"
---

# Universal AI Agent Audit: HTTP Layer Validation

Dedicated prompt for AI Agents to validate HTTP Layer (`features/Api/`) compliance against standards: **Thin Action**, **FormRequest (`rules()` + `getDto()`)**, **Custom Resource (`XxxRes`)**, and **Controller**.

---

## 🎯 HTTP Layer Inspection Focus

### 1. Action Validation (`features/Api/**/*/Action.php`):
- [ ] Action length is **≤ 20 lines**.
- [ ] **NO** direct access to `DB::` or Eloquent `Model::`.
- [ ] **NO** loops (`foreach`, `array_map`) for domain transformation logic.
- [ ] **NO** complex business validation (domain validation belongs in Handlers/Entities).
- [ ] **DOES NOT RETURN** `JsonResponse` directly.
- [ ] **DOES NOT RETURN** internal DTOs or raw arrays.
- [ ] **ONLY RETURNS** a Custom Resource (`XxxRes`) via `ResService`.

### 2. FormRequest Validation (`features/Api/**/*/Request.php`):
- [ ] May have a `rules(): array` method for basic format validation (required, min, max, email).
- [ ] **MUST HAVE** a `getDto(): XxxDto` method to map request inputs to strongly-typed DTOs.
- [ ] Never pass the Laravel Request instance into the Application or Domain layers.

### 3. Custom Resource & ResService Validation:
- [ ] Resource is located in `features/Api/{Module}/Shared/XxxRes.php`.
- [ ] Resource implements `\JsonSerializable` (or extends `BaseRes`).
- [ ] **DOES NOT USE** the built-in `Illuminate\Http\Resources\Json\JsonResource` class.
- [ ] Value Object values are extracted using `$this->id->value()`.

### 4. Controller Validation (`features/Api/**/Controller.php`):
- [ ] Controller only delegates the input DTO from Request to Action: `$resource = $action($request->getDto());`.
- [ ] Controller is responsible for wrapping the Resource into a `JsonResponse`: `return response()->json($resource, 201);`.

---

## 📄 HTTP Validation Report Format (`docs/Reports/YYYY-MM-DD-validate-http-layer.md`)

```markdown
# HTTP Layer Validation Report

**Date:** YYYY-MM-DD  
**Auditor Agent:** [AI Agent Name]  

## 🔍 Inspection Findings

### 1. [Action/Request Name] ([File Location:Line])
- **Severity**: CRITICAL / WARNING
- **Violation**: [e.g., Action returning JsonResponse directly]
- **Original Code**:
  ```php
  public function __invoke(CreateBookingDto $dto): JsonResponse { ... }
  ```
- **Recommended Refactoring**:
  ```php
  public function __invoke(CreateBookingDto $dto): BookingCreatedRes { ... }
  ```

## 📋 Remediation Action Summary
- [ ] Change Action return type to `XxxRes`.
- [ ] Ensure Request contains `getDto()`.
- [ ] Relocate `response()->json(...)` conversion to the Controller.
```
