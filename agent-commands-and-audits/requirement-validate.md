---
description: Validates requirement documents (epics, features, hotfixes, cases)
proactive: false
triggers:
  - "validate requirement"
  - "review requirement"
  - "/requirement-validate"
---

# Universal AI Agent Command: Requirement Validation

Prompt bagi AI Agent untuk memvalidasi kelengkapan dokumen kebutuhan (*requirements*) yang berada di `docs/working_docs/`.

---

## 🧭 Prinsip Utama: "Analysis Informs, Never Blocks"
- Evaluasi dokumen untuk menemukan risiko, celah logika, dan ketergantungan tersembunyi.
- Jangan memblokir user; berikan informasi yang jelas dan biarkan user memutuskan.

---

## 🔍 Alur Validasi Sesuai Tipe Dokumen:

### 1. Deteksi Tipe Requirement:
- Jika path mengandung `/epics/` ➔ Jalankan **Validasi Penuh (Full Validation)**.
- Jika path mengandung `/features/` ➔ Jalankan **Validasi Sederhana (cek referensi ke parent epic)**.
- Jika path mengandung `/hotfixes/` ➔ Jalankan **Validasi Fokus Masalah & Rollback**.
- Jika path mengandung `/cases/` ➔ Jalankan **Validasi Investigasi (TIDAK BOLEH ADA KODE IMPLEMENTASI)**.

### 2. Checklist Pemeriksaan (Untuk Epic & Feature):
1. **Business Alignment**: Apakah tujuan bisnis dan KPI (atau hipotesis eksperimen) terdefinisi?
2. **Entitas & Siklus CRUD**: Apakah operasi Create, Read, Update, Delete, dan List sudah diperiksa?
3. **Analisis State & Transisi**: Apakah status awal, semua kemungkinan status, pemicu transisi, dan kondisi sudah terdefinisi?
4. **Operasi Invers**: Apakah aksi kebalikan (misal: cancel, deactivate, reject) sudah dipertimbangkan?
5. **Dampak Kolateral**: Apakah perubahan ini memengaruhi modul, endpoint, skema database, atau pelaporan yang sudah berjalan?
6. **Slicing & MVP**: Apakah scope terlalu besar? Jika ya, apakah pemotongan fase (MVP vs fase lanjutan) sudah logis?

---

## 📄 Format Output Hasil Validasi

AI Agent wajib menyajikan output analisis dalam format berikut:

```markdown
# Requirement Validation Report: [Nama Dokumen]

**Tipe Terdeteksi:** Epic / Feature / Hotfix / Case  
**Status Evaluasi:** Siap Diimplementasikan / Butuh Klarifikasi  

### 1. Temuan Use Case yang Berpotensi Hilang (Missing Use Cases)
| Use Case yang Terlewat | Alasan Diperlukan | Prioritas | Pertanyaan untuk Stakeholder |
|---|---|---|---|
| [Aksi] | [Alasan] | Must / Should | [Pertanyaan] |

### 2. Evaluasi Mesin State & Transisi
- **Status Awal**: [Terdefinisi / Belum]
- **Celah Transisi**: [Jelaskan jika ada status gantung tanpa jalan keluar]

### 3. Analisis Dampak Kolateral (Collateral Impact)
- **Komponen Terdampak**: [Daftar modul/tabel yang terpengaruh]
- **Potensi Risiko**: [Risiko teknis / regresi fitur lama]

### 4. Evaluasi Slicing & MVP
- [Rekomendasi apakah ukuran fitur sudah pas atau perlu dipecah ke Slice 1, Slice 2]

### 5. Pertanyaan Klarifikasi Terbuka
1. [Pertanyaan spesifik yang butuh jawaban user]
```
