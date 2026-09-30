# Documentation Governance & Evolution Policy

Dokumentasi ini mengatur aturan pemeliharaan, pembaruan, dan siklus hidup seluruh dokumen di dalam repositori dokumentasi ini.

---

## 🏛️ Prinsip Utama: Pemisahan Prinsip vs Dokumen Hidup

Dokumentasi dibagi menjadi dua kategori dengan aturan pembaruan yang berbeda:

```
┌─────────────────────────────────────────────────────────────────┐
│                    DOKUMENTASI SISTEM                           │
├────────────────────────────────┬────────────────────────────────┤
│ 1. Prinsip Arsitektur          │ 2. Dokumen Hidup               │
│    (Architectural Invariants)  │    (Living Documents)          │
│                                │                                │
│ 🔒 INVARIAN / STATIS           │ 🔄 DAPAT DIUPDATE BERKALA      │
│ - DDD Core Rules               │ - Project Context              │
│ - Hexagonal Layering           │ - Requirements (Epic/Feature)  │
│ - Strict CQRS (void commands)  │ - Task Lists & Roadmaps        │
│ - Invariant Enforcements       │ - Bug & Incident Cases         │
│ - Performance & DB Rules       │ - Audit Reports                │
│                                │ - Helper Catalogs & Notes      │
└────────────────────────────────┴────────────────────────────────┘
```

---

## 1. Prinsip Arsitektur (Invarian / Statis)

Dokumen dalam kategori ini meliputi:
- `core-architecture/*` (Architecture Overview, Critical Rules, CQRS, Infrastructure, Code Quality)
- `presentation-layer/*` (HTTP Layer Patterns, Thin Actions, Custom Resources)
- `development-lifecycle/01-development-workflow.md` (3-Phase Implementation Order)

### Aturan:
1. **Aturan Invarian**: Prinsip-prinsip ini bersifat baku dan tidak boleh diubah sembarangan saat pengerjaan fitur sehari-hari.
2. **AI Agent Protection**: AI agent dilarang melonggarkan atau memodifikasi prinsip arsitektur tanpa persetujuan eksplisit dari tim/lead arsitek (misal: dilarang mengizinkan query di dalam loop, dilarang mengembalikan data dari Command, dilarang mengakses `DB::` langsung di Handler).
3. **Prosedur Perubahan**: Perubahan pada prinsip arsitektur hanya dapat dilakukan melalui **Architectural Decision Record (ADR)** formal yang disepakati oleh domain expert dan tech lead.

---

## 2. Dokumen Hidup (Dapat & Harus Diupdate Berkala)

Dokumen dalam kategori ini meliputi:
- `PROJECT_CONTEXT.md` (Profil, modul, konfigurasi, dan status project aktif)
- `development-lifecycle/02-requirements-engineering.md` & template
- Dokumen working docs (`docs/working_docs/epics/*`, `features/*`, `hotfixes/*`, `cases/*`)
- Dokumen pelaporan (`docs/Reports/YYYY-MM-DD-*.md`)
- Task list per fitur (`[feature]_tasks.md`)

### Aturan Pembaruan Berkala:
1. **Sinkronisasi Berkala**: Dokumen hidup **wajib dan dapat diperbarui secara berkala** seiring berjalannya sprint, penambahan modul baru, perubahan dependensi, atau penyelesaian milestone.
2. **Kewenangan AI Agent**:
   - AI agent **berhak dan dianjurkan** memperbarui `PROJECT_CONTEXT.md` ketika mendeteksi perubahan lingkungan, bounded context baru, migrasi skema baru, atau endpoint baru.
   - AI agent harus memperbarui task list status (checklist `[x]`) setiap kali suatu sub-task selesai diimplementasikan.
   - AI agent wajib membuat atau memperbarui dokumen audit/report ketika menjalankan proses validasi atau audit arsitektur.
3. **Kejelasan Historis**: Setiap pembaruan dokumen hidup harus mencantumkan konteks atau alasan perubahan (misalnya: penambahan Bounded Context baru, pembaruan versi PHP/Laravel, atau temuan investigasi insiden).

---

## 📋 Checklist bagi AI Agent Sebelum Memodifikasi Dokumen

Sebelum melakukan edit pada dokumen mana pun:

- [ ] **Identifikasi Kategori**: Apakah dokumen ini adalah *Prinsip Arsitektur* atau *Dokumen Hidup*?
- [ ] **Jika Prinsip Arsitektur**:
  - Apakah user secara eksplisit meminta perubahan aturan arsitektur?
  - Jika TIDAK, pertahankan aturan dan jangan ubah prinsip dasarnya.
- [ ] **Jika Dokumen Hidup**:
  - Perbarui data terbaru (status project, konfigurasi, bounded context, dll).
  - Pastikan format tetap rapi dan konsisten dengan template standar.
  - Perbarui timestamp tanggal pembaruan terakhir.
