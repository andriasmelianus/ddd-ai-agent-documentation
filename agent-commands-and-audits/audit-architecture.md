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

Prompt dan instruksi bagi AI Agent (Gemini, Claude, ChatGPT, Cursor, dll) untuk mengaudit kepatuhan kode terhadap aturan **Domain-Driven Design (DDD)**, **Hexagonal Architecture**, dan **CQRS**.

---

## 🎯 Instruksi Audit untuk AI Agent

Saat diminta melakukan audit arsitektur:
1. Scan seluruh codebase di bawah `Apps/` dan `src/`.
2. Analisis kode terhadap 8 area kepatuhan kritis di bawah ini.
3. Kelompokkan temuan berdasarkan tingkat keparahan: **CRITICAL**, **HIGH**, dan **MEDIUM**.
4. Simpan laporan hasil audit ke dalam file markdown di: `docs/Reports/YYYY-MM-DD-audit-architecture.md`.

---

## 🔍 8 Area Kepatuhan yang Wajib Diperiksa:

### 1. HTTP Layer — Actions (CRITICAL)
Lokasi: `Apps/Api/**/*/Action.php`
- ❌ **Pelanggaran**:
  - Memanggil `DB::table()`, `DB::statement()`, atau Query Builder.
  - Memanggil Eloquent Model (`Model::find()`, `Model::where()`).
  - Terdapat perulangan (`foreach`, `array_map`) untuk logika bisnis atau transformasi data domain.
  - Memiliki logika bisnis, kalkulasi harga/diskon, atau validasi domain.
  - Mengembalikan `JsonResponse` langsung (seharusnya mengembalikan Custom Resource `XxxRes`).
  - Mengembalikan DTO internal atau raw array.
  - Panjang method > 20 baris.
- ✅ **Standar**:
  - Hanya melakukan: verifikasi akses (JWT), dispatch command/query, dan mengembalikan `XxxRes` via `ResService`.

### 2. HTTP Layer — Requests (CRITICAL)
Lokasi: `Apps/Api/**/*/Request.php`
- ❌ **Pelanggaran**:
  - Tidak memiliki method `getDto(): XxxDto`.
  - Mengirimkan raw request data atau data tanpa tipe ke Action/Handler.
- ✅ **Standar**:
  - Boleh memiliki `rules(): array` untuk validasi format HTTP dasar.
  - Wajib memiliki `getDto(): XxxDto` untuk memetakan input ke strongly-typed DTO.

### 3. Application Layer — Handlers (CRITICAL)
Lokasi: `src/**/Application/**/Handler.php`
- ❌ **Pelanggaran**:
  - Menggunakan `DB::` langsung dalam bentuk apa pun.
  - Menggunakan Eloquent Model langsung.
- ✅ **Standar**:
  - Selalu inject `*RepositoryInterface` dari Domain.

### 4. CQRS — Commands (CRITICAL)
Lokasi: `src/**/Application/Commands/**/`
- ❌ **Pelanggaran**:
  - Command Handler mengembalikan nilai selain `void` (misal: `return $id;` atau return Entity).
- ✅ **Standar**:
  - Return type Handler adalah `void`.
  - ID digenerate sebelum command di-dispatch dan diteruskan sebagai parameter command.

### 5. Entity Invariants & Construction Pattern (CRITICAL)
Lokasi: `src/**/Domain/Entities/*.php`
- ❌ **Pelanggaran**:
  - Constructor `public` tanpa enkapsulasi invariant.
  - Terdapat public setter (`setStatus()`, `setItems()`).
  - Mapper menggunakan `ReflectionClass::newInstanceWithoutConstructor()`.
- ✅ **Standar**:
  - Constructor `private`.
  - Terdapat static method `create(...)` untuk data baru (dengan Domain Event).
  - Terdapat static method `reconstitute(...)` untuk hidrasi database (tanpa Domain Event).

### 6. Database Performance — Queries in Loops (CRITICAL)
Lokasi: Seluruh file di `src/` dan `Apps/`
- ❌ **Pelanggaran**:
  - Pemanggilan repository atau query database di dalam perulangan (`foreach`, `while`).
- ✅ **Standar**:
  - Ambil semua ID terlebih dahulu, lalu jalankan satu query dengan klausa `IN (...)`.

### 7. Value Objects — ID Typing (HIGH)
Lokasi: `src/**/Domain/Repositories/*Interface.php`
- ❌ **Pelanggaran**:
  - Parameter ID menggunakan tipe data primitif `string` atau `int`.
- ✅ **Standar**:
  - Parameter ID wajib menggunakan Value Object bertipe (contoh: `BookingId $id`, `ClientId $id`).

### 8. Bounded Context Boundaries (HIGH)
Lokasi: `src/**`
- ❌ **Pelanggaran**:
  - Entity atau Repository digunakan langsung oleh Bounded Context lain tanpa melalui QueryBus / CommandBus.
  - SQL JOIN langsung antar tabel milik Bounded Context yang berbeda.

---

## 📄 Format Laporan Audit (`docs/Reports/YYYY-MM-DD-audit-architecture.md`)

```markdown
# DDD and Hexagonal Architecture Audit Report

**Date:** YYYY-MM-DD  
**Auditor Agent:** [Nama AI Agent]  
**Status:** Completed  

## 🚨 CRITICAL VIOLATIONS

### 1. [Nama File]:[Nomor Baris]
- **Kategori**: [HTTP Action / Direct DB / Command Return]
- **Potongan Kode Bermasalah**:
  ```php
  // Kode pelanggaran
  ```
- **Penjelasan Masalah**: [Mengapa melanggar aturan arsitektur]
- **Solusi Perbaikan**:
  ```php
  // Kode perbaikan yang direkomendasikan
  ```

## ⚠️ HIGH & MEDIUM VIOLATIONS
[Daftar temuan lainnya]

## 📊 Ringkasan Statistik
- Critical: X temuan
- High: X temuan
- Medium: X temuan
- Total File Diperiksa: X file

## 🎯 Prioritas Tindakan
1. Perbaiki Action dan Handler yang menyentuh DB langsung.
2. Perbaiki signature Command Handler agar strictly void.
3. Refaktor hidrasi entity ke `reconstitute()`.
```
