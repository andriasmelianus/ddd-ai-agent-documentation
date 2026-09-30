# Multi-AI Agent Compatibility Guide

Dokumentasi ini dirancang agar **100% kompatibel dan agnostik** terhadap AI Agent mana pun yang Anda gunakan: **Gemini (Antigravity), Claude (Claude Code / Anthropic), OpenAI ChatGPT, Cursor, Windsurf, GitHub Copilot**, dan lainnya.

Berikut adalah panduan cara mengonfigurasi dan memanfaatkan paket dokumentasi ini pada berbagai AI agent:

---

## 🤖 1. Gemini / Google Antigravity

- **Di mana meletakkannya**: Anda dapat meletakkan folder dokumentasi ini di direktori project (misal: `docs/ai_docs/` atau `.gemini/rules/`) atau menyimpannya di lokasi terpusat seperti `/home/andrias/projects/shared/ddd-ai-agent-documentation/`.
- **System Prompt / Workspace Instruction**:
  Tambahkan instruksi berikut pada instruksi awal atau konfigurasi workspace:
  ```markdown
  Selalu patuhi aturan Domain-Driven Design (DDD), Hexagonal Architecture, dan CQRS yang tertera di:
  [ddd-ai-agent-documentation](file:///home/andrias/projects/shared/ddd-ai-agent-documentation/README.md)
  Sebelum memulai pengerjaan kode, baca `core-architecture/02-critical-rules.md` dan `PROJECT_CONTEXT.md`.
  ```

---

## 🧠 2. Claude (Claude Code / Anthropic)

- **Root Guide (`CLAUDE.md`)**:
  Buat file `CLAUDE.md` di root project Anda dengan isi sederhana yang merujuk ke dokumentasi bersama ini:
  ```markdown
  # Project Guidelines for Claude Code

  This project strictly follows Domain-Driven Design, Hexagonal Architecture, and CQRS.
  All architectural rules and standards are documented in:
  `/home/andrias/projects/shared/ddd-ai-agent-documentation/`

  Read `core-architecture/02-critical-rules.md` before generating or modifying any code.
  Follow the 3-Phase workflow in `development-lifecycle/01-development-workflow.md`.
  ```
- **Custom Slash Commands (`.claude/commands/`)**:
  Salin prompt dari direktori `agent-commands-and-audits/` ke dalam folder `.claude/commands/` di project Anda:
  - `agent-commands-and-audits/audit-architecture.md` → `.claude/commands/audit-architecture.md`
  - `agent-commands-and-audits/validate-http-layer.md` → `.claude/commands/validate-http-layer.md`
  - `agent-commands-and-audits/analyze-performance.md` → `.claude/commands/analyze-performance.md`

---

## ⚡ 3. Cursor IDE (`.cursorrules` atau `.cursor/rules/`)

- Buat file `.cursorrules` di root project, atau buat rules di folder `.cursor/rules/`:
  ```markdown
  You are an expert software engineer working on a Domain-Driven Design (DDD), Hexagonal Architecture, and CQRS project in PHP/Laravel.

  Always follow the architecture documentation located at:
  /home/andrias/projects/shared/ddd-ai-agent-documentation/

  Key Rules:
  1. Requests can have rules() for HTTP syntax validation AND getDto() for DTO mapping.
  2. Actions are thin (<= 20 lines) and return custom Resource (XxxRes), NEVER JsonResponse/DTO.
  3. Commands in CQRS ALWAYS return void.
  4. Handlers NEVER use DB:: directly (always use Repositories).
  5. Entities protect invariants with private constructors and reconstitute() for DB hydration.
  6. Never run SQL queries in loops (prevent N+1).
  ```

---

## 🌊 4. Windsurf (`.windsurfrules`)

- Buat file `.windsurfrules` di root project dengan referensi ke path dokumentasi yang sama:
  ```markdown
  Architectural Framework:
  Refer to `/home/andrias/projects/shared/ddd-ai-agent-documentation/` for DDD, Hexagonal Architecture, CQRS, and HTTP Layer patterns.
  Never violate `core-architecture/02-critical-rules.md`.
  ```

---

## 💬 5. OpenAI ChatGPT / Custom GPT / Copilot Chat

- **Untuk Custom GPT / System Instructions**:
  Unggah dokumen di direktori `core-architecture/`, `presentation-layer/`, dan `development-lifecycle/` sebagai Knowledge Base, atau salin ringkasan dari `README.md` dan `02-critical-rules.md` ke dalam field Instructions.
- **Untuk Chat Prompting**:
  Ketika meminta ChatGPT atau Copilot membuat fitur baru:
  ```
  Saya mengerjakan project Laravel dengan standar DDD, Hexagonal Architecture, dan CQRS.
  Patuhi aturan berikut:
  1. Request memiliki rules() untuk validasi format input dan getDto() untuk membuat Strongly-Typed DTO.
  2. Action tipis (maksimal 20 baris), hanya verifikasi akses, dispatch command/query, dan mengembalikan custom Resource (XxxRes).
  3. Controller mengubah XxxRes menjadi JsonResponse via response()->json($res).
  4. Command Handler wajib return void (ID di-generate sebelum dispatch).
  5. Entity menggunakan private constructor dengan static create() dan static reconstitute().
  ```

---

## 🎯 Ringkasan Kompatibilitas Agent

Semua dokumen di dalam repositori ini:
- Menggunakan format standar Markdown (`.md`) murni.
- Menghindari vendor-lock in atau sintaks tertutup.
- Memisahkan **definisi arsitektur** dari **prompt eksekusi audit**, sehingga prompt audit dapat dijalankan oleh agent mana pun baik melalui slash command maupun copy-paste instruksi.
