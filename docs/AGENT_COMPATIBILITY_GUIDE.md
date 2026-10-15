# Multi-AI Agent Compatibility Guide

This documentation is designed to be **100% compatible and agnostic** across any AI Agent you use: **Gemini (Antigravity), Claude (Claude Code / Anthropic), OpenAI ChatGPT, Cursor, Windsurf, GitHub Copilot**, and others.

Below is the guide on how to configure and utilize this documentation package across various AI agents:

---

## 🤖 1. Gemini / Google Antigravity

- **Where to place it**: You can place this documentation folder in your project directory (e.g., `docs/ai_docs/` or `.gemini/rules/`) or keep it in a centralized shared location such as `/home/borwita/projects/shared/ddd-ai-agent-documentation/`.
- **System Prompt / Workspace Instruction**:
  Add the following instructions to your initial instructions or workspace configuration:
  ```markdown
  Always adhere to the Domain-Driven Design (DDD), Hexagonal Architecture, and CQRS rules documented in:
  [ddd-ai-agent-documentation](file:///home/borwita/projects/shared/ddd-ai-agent-documentation/README.md)
  Before starting any code implementation, read `core-architecture/02-critical-rules.md` and `PROJECT_CONTEXT.md`.
  ```

---

## 🧠 2. Claude (Claude Code / Anthropic)

- **Root Guide (`CLAUDE.md`)**:
  Create a `CLAUDE.md` file in your project root with a straightforward reference to this shared documentation:
  ```markdown
  # Project Guidelines for Claude Code

  This project strictly follows Domain-Driven Design, Hexagonal Architecture, and CQRS.
  All architectural rules and standards are documented in:
  `/home/borwita/projects/shared/ddd-ai-agent-documentation/`

  Read `core-architecture/02-critical-rules.md` before generating or modifying any code.
  Follow the 3-Phase workflow in `development-lifecycle/01-development-workflow.md`.
  ```
- **Custom Slash Commands (`.claude/commands/`)**:
  Copy prompts from the `agent-commands-and-audits/` directory into your project's `.claude/commands/` folder:
  - `agent-commands-and-audits/audit-architecture.md` → `.claude/commands/audit-architecture.md`
  - `agent-commands-and-audits/validate-http-layer.md` → `.claude/commands/validate-http-layer.md`
  - `agent-commands-and-audits/analyze-performance.md` → `.claude/commands/analyze-performance.md`

---

## ⚡ 3. Cursor IDE (`.cursorrules` or `.cursor/rules/`)

- Create a `.cursorrules` file in your project root, or create rules under `.cursor/rules/`:
  ```markdown
  You are an expert software engineer working on a Domain-Driven Design (DDD), Hexagonal Architecture, and CQRS project in PHP/Laravel.

  Always follow the architecture documentation located at:
  /home/borwita/projects/shared/ddd-ai-agent-documentation/

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

- Create a `.windsurfrules` file in your project root referencing the same documentation path:
  ```markdown
  Architectural Framework:
  Refer to `/home/borwita/projects/shared/ddd-ai-agent-documentation/` for DDD, Hexagonal Architecture, CQRS, and HTTP Layer patterns.
  Never violate `core-architecture/02-critical-rules.md`.
  ```

---

## 💬 5. OpenAI ChatGPT / Custom GPT / Copilot Chat

- **For Custom GPT / System Instructions**:
  Upload documents from `core-architecture/`, `presentation-layer/`, and `development-lifecycle/` directories as Knowledge Base files, or copy the summary from `README.md` and `02-critical-rules.md` into the Instructions field.
- **For Chat Prompting**:
  When asking ChatGPT or Copilot to create a new feature:
  ```
  I am working on a Laravel project following DDD, Hexagonal Architecture, and CQRS standards.
  Adhere to the following rules:
  1. Requests have rules() for input format validation and getDto() to create Strongly-Typed DTOs.
  2. Actions are thin (maximum 20 lines), handling only access verification, command/query dispatch, and returning a custom Resource (XxxRes).
  3. Controllers convert XxxRes into JsonResponse via response()->json($res).
  4. Command Handlers must return void (IDs are generated before dispatch).
  5. Entities use private constructors with static create() and static reconstitute().
  ```

---

## 🎯 Summary of Agent Compatibility

All documents in this repository:
- Use pure standard Markdown (`.md`) format.
- Avoid vendor lock-in or proprietary syntax.
- Separate **architectural definitions** from **audit execution prompts**, enabling audit prompts to be executed by any agent via slash commands or copied instructions.
