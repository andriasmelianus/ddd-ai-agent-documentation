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

Prompt bagi AI Agent untuk menganalisis dan mengidentifikasi potensi bottleneck performa database, pola N+1 query, ketiadaan index, dan pemborosan hidrasi memori.

---

## 🔍 Area Audit Performa Database:

### 1. Query di Dalam Perulangan (N+1 Query) — CRITICAL
Cari pola kode di mana pemanggilan repository atau query database terjadi di dalam `foreach`, `while`, atau `array_map`:
```php
// CONTOH PELANGGARAN N+1:
foreach ($bookings as $booking) {
    $client = $this->clientRepository->findById($booking->clientId); // FATAL: N queries!
}
```
**Rekomendasi Perbaikan**:
Kumpulkan ID terlebih dahulu, jalankan 1 query dengan `WHERE IN`, lalu gabungkan di memori PHP:
```php
$clientIds = array_map(static fn($b) => $b->clientId, $bookings);
$clients = $this->clientRepository->findByIds($clientIds);
$clientsMap = array_column($clients, null, 'id');
```

### 2. Direct DB:: di dalam Application Handlers — CRITICAL
Cari pemanggilan langsung `DB::table(...)` di dalam Handler Application Layer. Pindahkan ke method Repository teroptimasi.

### 3. Ketiadaan Eager Loading pada Relasi Model — HIGH
Cari pemanggilan properti relasi Eloquent tanpa pemanggilan `with(...)`:
```php
// PELANGGARAN:
$bookings = BookingModel::all();
foreach ($bookings as $b) {
    echo $b->restaurant->name; // Memicu lazy loading berulang
}

// PERBAIKAN:
$bookings = BookingModel::query()->with('restaurant')->get();
```

### 4. Ketiadaan Index pada Kolom Kunci — HIGH
Periksa definisi migrasi skema tabel untuk:
- Kolom Foreign Key (contoh: `restaurant_id`, `client_id`) yang belum memiliki index.
- Kolom status atau enum yang sering digunakan dalam filter `WHERE`.
- Siapkan skrip migrasi penambahan index jika ditemukan kolom yang belum ter-index.

### 5. Ketiadaan Paginasi pada Data Besar — MEDIUM
Cari query yang mengembalikan seluruh data tanpa limit atau paginasi (`getAllClients()`). Rekomendasikan `CursorPagination` atau `OffsetLimitPagination`.

---

## 📄 Format Laporan Analisis Performa (`docs/Reports/YYYY-MM-DD-analyze-performance.md`)

```markdown
# Database Performance Analysis Report

**Date:** YYYY-MM-DD  
**Auditor Agent:** [Nama AI Agent]  

## 🚨 CRITICAL PERFORMANCE ISSUES (N+1 Queries)

### 1. [Lokasi File:Baris]
- **Operasi**: [Query di dalam perulangan foreach]
- **Estimasi Dampak**: [Misal: 500 query tambahan per eksekusi request]
- **Solusi Kode Sebelum & Sesudah**:
  ```php
  // Solusi bulk query dengan IN clause
  ```

## ⚠️ MISSING INDEXES
- **Tabel**: `bookings`
- **Kolom**: `client_id`
- **Rekomendasi Migrasi**:
  ```php
  Schema::table('bookings', function (Blueprint $table) {
      $table->index('client_id');
  });
  ```

## 📊 Estimasi ROI Optimalisasi
- Potensi pengurangan beban query: ~70-85%
- Estimasi perbaikan response time: ~50-60%
```
