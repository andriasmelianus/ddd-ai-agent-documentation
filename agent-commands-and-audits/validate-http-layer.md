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

Prompt khusus bagi AI Agent untuk memvalidasi kepatuhan komponen HTTP Layer (`Apps/Api/`) terhadap standar: **Thin Action**, **FormRequest (`rules()` + `getDto()`)**, **Custom Resource (`XxxRes`)**, dan **Controller**.

---

## 🎯 Fokus Pemeriksaan HTTP Layer

### 1. Validasi Action (`Apps/Api/**/*/Action.php`):
- [ ] Panjang Action **≤ 20 baris**.
- [ ] **TIDAK ADA** akses langsung ke `DB::` atau Eloquent `Model::`.
- [ ] **TIDAK ADA** perulangan (`foreach`, `array_map`) untuk logika transformasi domain.
- [ ] **TIDAK ADA** validasi bisnis rumit (validasi domain berada di Handler/Entity).
- [ ] **TIDAK MENGEMBALIKAN** `JsonResponse` secara langsung.
- [ ] **TIDAK MENGEMBALIKAN** DTO internal atau array mentah.
- [ ] **HANYA MENGEMBALIKAN** Custom Resource (`XxxRes`) via `ResService`.

### 2. Validasi FormRequest (`Apps/Api/**/*/Request.php`):
- [ ] Boleh memiliki method `rules(): array` untuk validasi format dasar (required, min, max, email).
- [ ] **WAJIB MEMILIKI** method `getDto(): XxxDto` untuk memetakan input request ke strongly-typed DTO.
- [ ] Tidak meneruskan instance Request Laravel ke lapisan Application atau Domain.

### 3. Validasi Custom Resource & ResService:
- [ ] Resource berada di `Apps/Api/{Modul}/Shared/XxxRes.php`.
- [ ] Resource mengimplementasikan `\JsonSerializable` (atau meng-extend `BaseRes`).
- [ ] **TIDAK MENGGUNAKAN** class bawaan `Illuminate\Http\Resources\Json\JsonResource`.
- [ ] Nilai Value Objects diekstrak menggunakan `$this->id->value()`.

### 4. Validasi Controller (`Apps/Api/**/Controller.php`):
- [ ] Controller hanya mendelegasikan input DTO dari Request ke Action: `$resource = $action($request->getDto());`.
- [ ] Controller bertanggung jawab membungkus Resource menjadi `JsonResponse`: `return response()->json($resource, 201);`.

---

## 📄 Format Laporan Validasi HTTP (`docs/Reports/YYYY-MM-DD-validate-http-layer.md`)

```markdown
# HTTP Layer Validation Report

**Date:** YYYY-MM-DD  
**Auditor Agent:** [Nama AI Agent]  

## 🔍 Temuan Pemeriksaan

### 1. [Nama Action/Request] ([Lokasi File:Baris])
- **Tingkat Keparahan**: CRITICAL / WARNING
- **Pelanggaran**: [contoh: Action mengembalikan JsonResponse langsung]
- **Kode Asli**:
  ```php
  public function __invoke(CreateBookingDto $dto): JsonResponse { ... }
  ```
- **Rekomendasi Refaktor**:
  ```php
  public function __invoke(CreateBookingDto $dto): BookingCreatedRes { ... }
  ```

## 📋 Ringkasan Aksi Perbaikan
- [ ] Ubah return type Action menjadi `XxxRes`.
- [ ] Pastikan Request memiliki `getDto()`.
- [ ] Pindahkan konversi `response()->json(...)` ke Controller.
```
