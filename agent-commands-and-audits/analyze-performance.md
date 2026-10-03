---
description: Analyzes performance issues in queries, database, and N+1 patterns
proactive: true
triggers:
  - "slow"
  - "performance"
  - "optimize"
  - "N+1"
  - "too many queries"
  - "analyze performance"
---

# Universal AI Agent Audit: Database Performance & N+1 Query Analysis

Prompt for AI Agents to analyze and identify potential database performance bottlenecks, N+1 query patterns, missing indexes, and wasteful memory hydration.

---

## 🔍 Database Performance Audit Areas:

### 1. Queries Inside Loops (N+1 Queries) — CRITICAL
Search for code patterns where repository calls or database queries occur inside `foreach`, `while`, or `array_map`:
```php
// N+1 VIOLATION EXAMPLE:
foreach ($bookings as $booking) {
    $client = $this->clientRepository->findById($booking->clientId); // FATAL: N queries!
}
```
**Recommended Fix**:
Collect IDs upfront, execute 1 query with `WHERE IN`, then join in PHP memory:
```php
$clientIds = array_map(static fn($b) => $b->clientId, $bookings);
$clients = $this->clientRepository->findByIds($clientIds);
$clientsMap = array_column($clients, null, 'id');
```

### 2. Direct DB:: Inside Application Handlers — CRITICAL
Search for direct `DB::table(...)` calls inside Application Layer Handlers. Move them to optimized Repository methods.

### 3. Missing Eager Loading on Model Relations — HIGH
Search for Eloquent relation property accesses without preceding `with(...)` calls:
```php
// VIOLATION:
$bookings = BookingModel::all();
foreach ($bookings as $b) {
    echo $b->restaurant->name; // Triggers repeated lazy loading
}

// FIX:
$bookings = BookingModel::query()->with('restaurant')->get();
```

### 4. Missing Indexes on Key Columns — HIGH
Inspect table migration schema definitions for:
- Foreign key columns (e.g., `restaurant_id`, `client_id`) lacking indexes.
- Status or enum columns frequently utilized in `WHERE` filter clauses.
- Prepare index addition migration scripts when unindexed columns are detected.

### 5. Missing Pagination on Large Datasets — MEDIUM
Identify queries returning unbounded datasets without limits or pagination (`getAllClients()`). Recommend `CursorPagination` or `OffsetLimitPagination`.

---

## 📄 Performance Analysis Report Format (`docs/Reports/YYYY-MM-DD-analyze-performance.md`)

```markdown
# Database Performance Analysis Report

**Date:** YYYY-MM-DD  
**Auditor Agent:** [AI Agent Name]  

## 🚨 CRITICAL PERFORMANCE ISSUES (N+1 Queries)

### 1. [File Location:Line]
- **Operation**: [Query inside foreach loop]
- **Estimated Impact**: [e.g., 500 extra queries per request execution]
- **Before & After Code Solution**:
  ```php
  // Bulk query solution with IN clause
  ```

## ⚠️ MISSING INDEXES
- **Table**: `bookings`
- **Column**: `client_id`
- **Migration Recommendation**:
  ```php
  Schema::table('bookings', function (Blueprint $table) {
      $table->index('client_id');
  });
  ```

## 📊 Estimated Optimization ROI
- Potential query load reduction: ~70-85%
- Estimated response time improvement: ~50-60%
```
