---
description: Designs technical architecture and tasks for a validated requirement
proactive: false
triggers:
  - "design solution"
  - "architect feature"
  - "/requirement-design-solution"
---

# Universal AI Agent Command: Technical Solution Design

Prompt bagi AI Agent untuk merancang solusi teknis arsitektur (Technical Design & Task Breakdown) dari dokumen kebutuhan yang sudah tervalidasi.

> ⚠️ **CATATAN**: Perintah ini **TIDAK BOLEH DIGUNAKAN UNTUK TIPE CASE**. Case hanya untuk investigasi kejadian/insiden.

---

## 🎯 Tanggung Jawab AI Agent Saat Mendesain Solusi

1. Baca dokumen requirement (`*_requirements.md`).
2. Rancang pemetaan arsitektur sesuai aturan DDD, Hexagonal, dan CQRS:
   - Tentukan Bounded Context sasaran.
   - Rancang Aggregate, Entity, Value Objects, dan Enums (Fase 1).
   - Rancang Port Interface, Model, Migration, dan Mapper (Fase 2).
   - Rancang Command, Query, FormRequest, Action, ResService, dan Controller (Fase 3).
3. Buat dokumen desain teknis di folder yang sama: `{nama-fitur}_design.md`.
4. Buat dokumen master task list di folder yang sama: `{nama-fitur}_tasks.md`.

---

## 📄 Format Dokumen Desain Teknis (`{nama-fitur}_design.md`)

```markdown
# Technical Design: [Nama Fitur]

**Requirement Reference:** [Tautan ke requirements.md]  
**Bounded Context:** `src/[NamaBoundedContext]/`  

## 1. Desain Model Domain (Phase 1)
- **Aggregate Root**: `[NamaEntity]`
- **Value Objects**: `[NamaId]`, `[ValueObjectLain]`
- **Enums**: `[StatusEnum]`
- **Domain Events**: `[NamaEventCreated]`, `[NamaEventUpdated]`

## 2. Desain Persistensi & Infrastruktur (Phase 2)
- **Tabel Database**: `[nama_tabel]`
- **Repository Interface**: `[NamaEntity]RepositoryInterface`
- **Metode Hidrasi**: `reconstitute()` pada entity

## 3. Desain Application & HTTP Layer (Phase 3)
- **Commands (Write - return void)**: `[CreateFiturCommand]`
- **Queries (Read - return DTO)**: `[GetFiturQuery]`
- **HTTP Endpoint**: `POST /api/[endpoint]`
- **Request**: `[Fitur]Request` (rules + getDto)
- **Action**: `[Fitur]Action` (thin, delegasi ke CommandBus, return Custom Res)
- **Custom Resource**: `[Fitur]Res` implements `JsonSerializable`
```

---

## 📋 Format Dokumen Master Task List (`{nama-fitur}_tasks.md`)

```markdown
# Task List: [Nama Fitur]

### Phase 1: Domain Layer
- [ ] Buat Value Objects & Enums
- [ ] Buat Domain Entity dengan private constructor & static create()
- [ ] Buat Domain Events & method transisi status
- [ ] Buat Unit Test Domain

### Phase 2: Infrastructure Layer
- [ ] Buat Repository Interface di Domain
- [ ] Buat skrip Database Migration (termasuk foreign key index)
- [ ] Buat Eloquent Model
- [ ] Buat Mapper (toDomain via reconstitute() dan toModel)
- [ ] Buat Repository Implementation
- [ ] Registrasi binding di Service Provider
- [ ] Buat Integration Test Repository

### Phase 3: Application & HTTP Layer
- [ ] Buat Command & Command Handler (strictly void)
- [ ] Buat Query, Query Handler, dan DTO/ReadModel
- [ ] Buat FormRequest dengan rules() & getDto()
- [ ] Buat Action tipis (≤ 20 baris, return XxxRes)
- [ ] Buat Custom API Resource (XxxRes) & ResService
- [ ] Daftarkan endpoint di Controller & routes/api.php
- [ ] Buat End-to-End API Feature Test
```
