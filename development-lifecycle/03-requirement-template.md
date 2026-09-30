# 03 - Requirement Document Templates

Template standar untuk dokumen kebutuhan di dalam `docs/working_docs/`: **Epic**, **Feature**, **Hotfix**, dan **Case (Investigation)**.

---

## 🏛️ Template 1: EPIC (`docs/working_docs/epics/{epic-name}/{epic-name}_requirements.md`)

```markdown
# Epic: [Nama Inisiatif Bisnis]

**Epic ID:** EPIC-001  
**Owner / Domain Expert:** [Nama Person]  
**Status:** Draft / Validated / In Progress / Completed  
**Target Deadline:** YYYY-MM-DD (jika ada)  

## 1. Business Alignment & Justifikasi
- **Tujuan Utama Perusahaan**: [Tumbuhkan Revenue / Turunkan Churn / Efisiensi Operasional]
- **Target KPI**:
  - Baseline: [Kondisi saat ini, misal: Occupancy rate 60%]
  - Target: [Target setelah rilis, misal: Occupancy rate 80%]
- **Bukti / Data Pendukung**: [Tiket komplain, permintaan kontrak klien, data analitik]

## 2. Deskripsi Masalah & Ruang Lingkup
- **Masalah Saat Ini**: [Penjelasan masalah pengguna]
- **Solusi yang Diharapkan**: [Ringkasan kapabilitas baru]
- **Entitas Utama**: [Daftar entitas domain yang terlibat]

## 3. Analisis State & Lifecycle Entitas
- **Initial Status**: [misal: DRAFT / PENDING]
- **Status Valid**: [DRAFT, ACTIVE, PAUSED, CANCELLED]
- **Matriks Transisi**:
  - DRAFT -> ACTIVE (Trigger: Klik Terbitkan, Kondisi: Konten valid)
  - ACTIVE -> CANCELLED (Trigger: Klik Batalkan)

## 4. Use Cases & Acceptance Criteria
### Use Case 1: [Nama Use Case]
- **Aktor**: [User / Admin / System]
- **Preconditions**: [Kondisi awal]
- **Alur Utama**:
  1. Pengguna memilih...
  2. Sistem memvalidasi...
- **Kriteria Penerimaan (Acceptance Criteria)**:
  - [ ] Given X, When Y, Then Z

## 5. Rencana Slicing (Tahapan Rilis)
- **Slice 1 (MVP)**: [Fitur pokok yang bisa langsung dites]
- **Slice 2**: [Fitur pendukung / manajemen lanjutan]
- **Out of Scope (Fase Ini)**: [Apa yang sengaja ditunda]

## 6. Analisis Dampak Kolateral
- **Komponen Terdampak**: [Modul/Tabel lain yang terpengaruh]
- **Kebutuhan Migrasi Data**: [Ada/Tidak, jika ada sebutkan skenarionya]
- **Risiko & Mitigasi**: [Risiko teknis / bisnis dan cara pencegahannya]

## 7. Definition of Done (DoD)
- [ ] Kode mematuhi aturan DDD & CQRS
- [ ] Unit test domain lulus 100%
- [ ] PHPStan 0 error
- [ ] UAT diterima oleh Product Owner
```

---

## 🧩 Template 2: FEATURE (`docs/working_docs/features/{feature-name}/{feature-name}_requirements.md`)

```markdown
# Feature: [Nama Fitur]

**Parent Epic:** [Tautan ke Dokumen Parent Epic](../../epics/{epic-name}/{epic-name}_requirements.md)  
**Feature Scope:** [Bagian slice dari Epic mana yang dikerjakan]  
**Status:** Draft / In Development / Done  

## 1. Konteks Singkat
Menginduk ke parent epic. Fitur ini secara khusus menyelesaikan [tujuan spesifik fitur].

## 2. Spesifikasi Fungsional & Kriteria Penerimaan
### Skenario 1: [Nama Skenario]
- **Given**: [Kondisi prasyarat]
- **When**: [Tindakan user/sistem]
- **Then**: [Hasil yang diharapkan]

## 3. Input & Output Data
- **Payload Request**: [Field, tipe data, validasi yang dibutuhkan]
- **Format Response**: [Struktur Resource response]

## 4. Testing & Definition of Done
- [ ] Unit test Handler & Entity
- [ ] Integration test HTTP Endpoint
- [ ] Review kepatuhan arsitektur
```

---

## 🚨 Template 3: HOTFIX (`docs/working_docs/hotfixes/HF-YYYY-XXX-{slug}/hotfix_requirements.md`)

```markdown
# Hotfix: [Deskripsi Masalah Mendesak]

**Hotfix ID:** HF-2026-001  
**Tingkat Keparahan:** Critical / High  
**Tanggal Dilaporkan:** YYYY-MM-DD  
**Pengguna/Layanan Terdampak:** [Siapa saja yang terkena dampak error]  

## 1. Deskripsi Masalah
[Gejala yang timbul di produksi, HTTP status error, atau stack trace]

## 2. Dampak Bisnis & Operasional
- Estimasi transaksi gagal: [Jumlah]
- Dampak langsung: [Kehilangan data / komplain user]

## 3. Akar Masalah (Root Cause)
[Penyebab teknis ditemukannya bug, file dan baris yang bermasalah]

## 4. Solusi Teknis yang Diajukan
[Rencana perbaikan kode]

## 5. Verifikasi & Pengujian
- [ ] Langkah reproduksi bug di lokal
- [ ] Verifikasi perbaikan
- [ ] Uji regresi komponen terkait

## 6. Rencana Rollback (Rollback Plan)
[Langkah cepat mengembalikan kode/migrasi jika hotfix menimbulkan efek samping baru]
```

---

## 🔬 Template 4: CASE (Investigation Only) (`docs/working_docs/cases/CASE-YYYY-XXX-{slug}/case_report.md`)

> ⚠️ **CATATAN**: Dokumen ini **HANYA UNTUK INVESTIGASI DAN ANALISIS INSIDEN**. Dokumen ini **TIDAK BERISI IMPLEMENTASI KODE LANGSUNG**. Jika hasil investigasi memerlukan perbaikan teknis, buat dokumen Hotfix atau Feature terpisah.

```markdown
# Case: [Investigasi Insiden / Analisis Permasalahan]

**Case ID:** CASE-2026-001  
**Tanggal Kejadian:** YYYY-MM-DD  
**Status Investigasi:** Open / In Progress / Closed  
**Investigator:** [Nama Engineer / AI Agent]  

## 1. Deskripsi Insiden
[Penjelasan apa yang terjadi dan bagaimana insiden pertama kali terdeteksi]

## 2. Kronologi Waktu (Timeline of Events)
- **10:00 UTC**: Insiden mulai terdeteksi di monitoring.
- **10:15 UTC**: Notifikasi alert dikirimkan ke tim.
- **10:45 UTC**: Investigasi log dan database dimulai.

## 3. Temuan Investigasi (Findings)
1. [Temuan 1: log error, anomali data]
2. [Temuan 2: query yang lambat atau memory leak]

## 4. Analisis Akar Masalah (Root Cause Analysis - 5 Whys)
[Penjelasan mendalam mengapa insiden bisa terjadi]

## 5. Rekomendasi Tindak Lanjut
1. **Perbaikan Segera (Immediate Action)**: [Rekomendasi pembuatan Hotfix HF-XXX]
2. **Pencegahan Jangka Panjang**: [Rekomendasi pembuatan Epic/Feature atau penambahan index/monitoring]
3. **Penyempurnaan Proses**: [Update SOP deployment / checklist migrasi]
```
