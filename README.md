# DDD, Hexagonal Architecture & CQRS — Universal AI Agent Documentation

> Repositori terpusat dan agnostik untuk dokumentasi, panduan arsitektur, standar kode, dan prompt audit bagi **AI Agent** (Gemini, Claude, ChatGPT, Cursor, Windsurf, Copilot) dan developer manusia dalam mengembangkan aplikasi berbasis **Domain-Driven Design (DDD)**, **Hexagonal Architecture (Ports and Adapters)**, dan **CQRS** pada ekosistem PHP/Laravel.

---

## 🚀 Panduan Memulai Cepat untuk AI Agent (Quick Start)

Jika Anda adalah AI Agent yang ditugaskan pada suatu project yang mengadopsi standar ini, **IKUTI URUTAN BACA BERIKUT**:

1. 📌 **[PROJECT_CONTEXT.md](PROJECT_CONTEXT.md)** — Baca konteks domain, modul aktif, dan spesifikasi project saat ini.
2. 🚨 **[02-critical-rules.md](core-architecture/02-critical-rules.md)** — **WAJIB DIBACA**: Aturan non-negotiable yang tidak boleh dilanggar.
3. 🏛️ **[01-architecture-overview.md](core-architecture/01-architecture-overview.md)** — Pahami pemisahan lapisan dan batas Bounded Context.
4. 🔄 **[01-development-workflow.md](development-lifecycle/01-development-workflow.md)** — Terapkan urutan implementasi 3 fase (Domain ➔ Infra ➔ Application/HTTP).

---

## 📚 Indeks Dokumentasi Lengkap

### 🏛️ 1. Prinsip & Inti Arsitektur (`core-architecture/`)
*Kategori: 🔒 Prinsip Arsitektur Invarian (Baku & Statis)*
- **[01 - Architecture Overview](core-architecture/01-architecture-overview.md)**: Gambaran umum DDD, Hexagonal Architecture, Bounded Contexts, komunikasi via Bus, dan aturan dependensi ke dalam.
- **[02 - Critical Rules](core-architecture/02-critical-rules.md)**: Aturan paling kritis (Command return void, performa query bebas N+1, Handler dilarang akses DB langsung, proteksi invariant entity, ID sebagai Value Object).
- **[03 - Application Layer & CQRS](core-architecture/03-application-layer-cqrs.md)**: Queries (Read), Commands (Write), Process Managers (Sagas/Cron), dan Domain Event Listeners.
- **[04 - Infrastructure Layer](core-architecture/04-infrastructure-layer.md)**: Repositories, ReadModels (pengganti array mixed), Table Objects, Hydrator/Mapper dengan `reconstitute()`, dan Eloquent Models.
- **[05 - Code Quality Principles](core-architecture/05-code-quality-principles.md)**: Prinsip SOLID, Stateless Services, Rich Entity vs Domain Service, Enums berdaya guna, dan standar penamaan domain.

### 🌐 2. Lapisan Presentasi HTTP (`presentation-layer/`)
*Kategori: 🔒 Prinsip Arsitektur Invarian (Baku & Statis)*
- **[HTTP Layer Architecture Patterns](presentation-layer/http-layer-patterns.md)**: Pola lengkap **Action-Request-Dto-Res**:
  - `FormRequest`: validasi format via `rules(): array` dan mapping ke DTO via `getDto(): XxxDto`.
  - `Action`: orkestrator tipis (≤ 20 baris), hanya verifikasi akses, dispatch Bus, dan return Custom Resource `XxxRes`.
  - `Custom Resource (XxxRes)`: implements `\JsonSerializable` (bukan Laravel `JsonResource`).
  - `Controller`: mengonversi Resource ke `response()->json($resource, $status)`.

### 🔄 3. Siklus Hidup & Kebutuhan Fitur (`development-lifecycle/`)
*Kategori: 🔄 Dokumen Hidup (Dapat & Harus Diupdate Berkala)*
- **[01 - Development Workflow](development-lifecycle/01-development-workflow.md)**: Urutan wajib 3 Fase pembangunan fitur (Fase 1: Domain ➔ Fase 2: Infra ➔ Fase 3: App & HTTP) beserta checklist verifikasinya.
- **[02 - Requirements Engineering](development-lifecycle/02-requirements-engineering.md)**: Panduan analisis kebutuhan ("Analysis Informs, Never Blocks"), CRUD check, analisis state/lifecycle, dan mitigasi dampak kolateral.
- **[03 - Requirement Templates](development-lifecycle/03-requirement-template.md)**: Template baku untuk **Epic**, **Feature**, **Hotfix**, dan **Case (Investigation Only)**.

### 🤖 4. Perintah & Audit AI Agent (`agent-commands-and-audits/`)
*Kategori: 🛠️ Prompt Audit Agnostik (Bisa dijalankan oleh Agent apa pun)*
- **[audit-architecture.md](agent-commands-and-audits/audit-architecture.md)**: Prompt audit menyeluruh kepatuhan DDD, Hexagonal, dan CQRS.
- **[validate-http-layer.md](agent-commands-and-audits/validate-http-layer.md)**: Prompt audit khusus HTTP Layer (Action tipis, Request getDto, Custom Res).
- **[analyze-performance.md](agent-commands-and-audits/analyze-performance.md)**: Prompt audit N+1 query, eager loading, dan ketiadaan index database.
- **[requirement-validate.md](agent-commands-and-audits/requirement-validate.md)**: Prompt untuk memvalidasi kelengkapan dokumen requirement.
- **[requirement-design-solution.md](agent-commands-and-audits/requirement-design-solution.md)**: Prompt untuk merancang solusi teknis arsitektur dari requirement.

### ⚙️ 5. Tata Kelola & Kompatibilitas Agent
- **[PROJECT_CONTEXT.md](PROJECT_CONTEXT.md)**: Dokumen profil dan konteks spesifik project yang sedang dikerjakan (*living document*).
- **[DOCUMENTATION_GOVERNANCE.md](DOCUMENTATION_GOVERNANCE.md)**: Kebijakan pembaruan dokumen (perbedaan antara prinsip arsitektur invarian vs dokumen yang diupdate berkala).
- **[AGENT_COMPATIBILITY_GUIDE.md](AGENT_COMPATIBILITY_GUIDE.md)**: Panduan konfigurasi untuk Claude Code, Gemini/Antigravity, Cursor, Windsurf, dan ChatGPT.

---

## 🚨 Ringkasan 7 Aturan Kritis (Golden Rules)

```
1. STRICT CQRS      : Commands ALWAYS return void (ID di-generate SEBELUM dispatch).
2. NO N+1 QUERIES   : Dilarang menjalankan query di dalam perulangan (kumpulkan ID, gunakan WHERE IN).
3. NO DB IN HANDLERS: Handler Application DILARANG menyentuh DB:: langsung (selalu lewat Repository).
4. IDS AS VALUE OBJS: Parameter ID pada domain interface adalah Value Object, BUKAN raw string.
5. INVARIANT ENTITY : Entity memiliki private constructor; gunakan create() dan reconstitute() (NO Reflection).
6. THIN ACTIONS     : Action ≤ 20 baris, dilarang logika bisnis, SELALU mengembalikan Custom Resource (XxxRes).
7. TYPED REQUESTS   : Request memiliki rules() untuk format HTTP dan getDto() untuk strongly-typed DTO.
```

---

## 🏛️ Kebijakan Pembaruan Dokumen

Berdasarkan [DOCUMENTATION_GOVERNANCE.md](DOCUMENTATION_GOVERNANCE.md):
- **Prinsip Arsitektur** (`core-architecture/*`, `presentation-layer/*`): Bersifat invarian dan tidak diubah tanpa kesepakatan tim arsitek.
- **Dokumen Hidup** (`PROJECT_CONTEXT.md`, task lists, requirements, audit reports): **Wajib dan dapat diupdate secara berkala** oleh AI Agent atau developer saat proyek berkembang.
