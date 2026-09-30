# 02 - Requirements Engineering & Analysis Guide

Panduan bagi AI Agent dan developer dalam menganalisis, memvalidasi, dan menyusun dokumen kebutuhan (*requirements*) sebelum masuk ke tahap perancangan teknis dan pengodean.

---

## 💡 Filosofi Inti: "Analysis Informs, Never Blocks"

**PENGGUNA (USER) SELALU MEMEGANG KEPUTUSAN AKHIR.**

| Prinsip | Makna Praktis |
|---|---|
| **Analisis bersifat informatif** | Menyoroti risiko, celah logika, dan dampak kolateral — **BUKAN** memblokir pekerjaan. |
| **User memutuskan** | Jika user berkata "lanjutkan / proceed", kita langsung mengeksekusi instruksi mereka. |
| **Bukan birokrasi kaku** | Definisi matang sangat berharga untuk mencegah tambal sulam (*parches sobre parches*), namun tidak boleh menjadi penghambat laju kerja. |

> 🤖 **Panduan Respons AI**:
> - ❌ JANGAN PERNAH berkata: *"Tidak bisa melanjutkan sampai X dilengkapi."*
> - ✅ SELALU katakan: *"Informasi X belum terdefinisi atau memiliki risiko Y. Apakah Anda ingin mendefinisikannya sekarang atau langsung melanjutkan implementasi?"*

---

## 🗂️ 4 Kategori Requirement & Folder Kerja

Dokumen requirement disimpan di dalam `docs/working_docs/`:

```
docs/working_docs/
├── epics/           # Inisiatif besar dengan justifikasi bisnis lengkap
├── features/        # Fitur spesifik yang menginduk ke suatu Epic
├── hotfixes/        # Perbaikan darurat bug di lingkungan produksi
└── cases/           # Investigasi insiden teknis (HANYA INVESTIGASI, BUKAN KODE)
```

### Karakteristik Masing-Masing Tipe:

| Tipe | Tujuan | Justifikasi Bisnis | Tingkat Analisis | Ada Koding? |
|---|---|---|---|---|
| **Epic** | Inisiatif skala besar | Wajib (KPI, ROI) | Penuh (CRUD, State, Slicing) | Ya (bertahap) |
| **Feature** | Bagian dari Epic | Referensi ke Parent Epic | Spesifik per fitur | Ya |
| **Hotfix** | Perbaikan darurat | Masalah bug = Justifikasi | Problem-focused + Rollback | Ya (langsung) |
| **Case** | Analisis insiden / bug | Tidak relevan | Investigasi Root Cause | **TIDAK ADA** |

---

## 🔍 Langkah Analisis Kebutuhan Step-by-Step

### Langkah 0: Deteksi Tipe Requirement
Deteksi tipe berdasarkan path dokumen (`/epics/`, `/features/`, `/hotfixes/`, `/cases/`).

---

### Langkah 1 & 2: Identifikasi Entitas & CRUD Check
Untuk setiap entitas bisnis utama yang terlibat, periksa kelengkapan siklus CRUD:
- **Create**: Bagaimana cara entitas ini dibuat?
- **Read / View**: Bagaimana cara detail entitas dilihat?
- **Update**: Data apa saja yang boleh diubah setelah dibuat?
- **Delete**: Bagaimana cara menghapusnya? (Hard delete vs Soft delete).
- **List / Search**: Bagaimana cara mencari atau memfilter daftar entitas ini?

---

### Langkah 3: Analisis Status & Mesin State (MANDATORY)
Hampir setiap entitas domain memiliki siklus hidup (*lifecycle*). AI wajib memverifikasi:
1. **Initial Status**: Apa status awal saat entitas pertama kali dibuat? (contoh: `DRAFT`, `PENDING`).
2. **Semua Kemungkinan Status**: Apa saja state yang valid?
3. **Valid Transitions**: Transisi apa saja yang legal? (contoh: dari `PENDING` -> `CONFIRMED`, tapi dilarang dari `CANCELLED` -> `CONFIRMED`).
4. **Trigger & Kondisi**: Apa pemicu tiap transisi? (Tindakan user, event otomatis, waktu kadaluarsa).
5. **Efek Samping**: Apakah transisi memicu domain event, notifikasi, atau perubahan entitas lain?

---

### Langkah 4 & 5: Pola Use Case & Operasi Invers (Kebalikan)
Setiap kali ada satu aksi bisnis yang diajukan, periksa apakah aksi pasangannya dibutuhkan:

| Aksi yang Diminta | Periksa Aksi Pasangan / Terkait |
|---|---|
| Buat Reservasi | Batalkan, Jadwalkan Ulang, Konfirmasi |
| Tambah Kontak | Hapus Kontak, Update Kontak |
| Aktifkan Fitur | Nonaktifkan Fitur |
| Setujui (Approve) | Tolak (Reject), Minta Revisi |
| Soft Delete | Restore (Kembalikan data) |

---

### Langkah 6: User Journey & Penanganan Kesalahan
- Apa prasyarat (*preconditions*) sebelum aksi dilakukan?
- Apa konsekuensi (*consequences*) setelah aksi berhasil?
- **Error Recovery**: Apa yang terjadi jika user melakukan kesalahan input?
- **Undo / Change Mind**: Bagaimana jika user berubah pikiran setelah menekan tombol submit?

---

### Langkah 7: Analisis Dampak Kolateral (Collateral Impact)
Fitur baru jarang berdiri sendiri. AI wajib menganalisis dampaknya ke sistem yang sudah berjalan:
1. **Breaking Changes**: Apakah perubahan ini merusak API contract atau database yang sudah ada?
2. **Behavioral Changes**: Apakah perhitungan logika lama akan berubah hasilnya?
3. **Data Impact**: Apakah data yang ada di database membutuhkan skrip migrasi data?
4. **UI & API Impact**: Layar atau endpoint mana saja yang terpengaruh?
5. **Performance Impact**: Apakah ada validasi baru yang berpotensi menambah query berat?

---

### Langkah 8: Slicing Strategy (Pemotongan Fitur Besar)
Jika requirement terlalu besar (> 7 use case, > 3 entitas, atau butuh waktu berminggu-minggu), **WAJIB dipecah menjadi beberapa slice/fase**:
- **Slice 1 (MVP)**: Alur paling kritis yang dapat memberikan nilai langsung (contoh: Create + View).
- **Slice 2**: Alur sekunder (Edit + Cancel).
- **Slice 3**: Fitur penyempurna (Notifikasi + Reporting).
- **Aturan Out of Scope**: Jangan membuang hal esensial (seperti validasi dan penanganan error dasar) ke "Out of Scope".

---

## 🚫 Anti-Patterns yang Wajib Diberi Tanda (Flagged)

| Anti-Pattern | Contoh Buruk | Contoh Perbaikan yang Baik |
|---|---|---|
| **Bahasa Ambigu** | *"Sistem harus cepat"* | *"Waktu respons API < 200ms pada p95"* |
| **Solusi sebagai Syarat** | *"Gunakan Redis untuk cache"* | *"Data sering diakses harus tampil < 50ms"* |
| **Lifecycle Sepotong** | *"Hanya buat screen Add Order"* | *"Tentukan lifecycle: Add, View, Cancel, Complete"* |
| **Abaikan Dampak Kolateral** | *"Tambah kolom diskon di order"* | *"Tambah diskon -> perbarui invoice, pajak, laporan"* |
| **CRUD Slicing Cacat** | *"Fase 1: Create Order. Fase 2: View Order"* | *"Fase 1 harus mencakup Create dan View agar bermanfaat"* |
| **Justifikasi Subjektif** | *"Banyak user minta fitur ini"* | *"15 customer tiket support meminta ini (total nilai kontrak €20k)"* |
