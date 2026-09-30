# 01 - Architecture Overview

Panduan komprehensif mengenai penerapan **Domain-Driven Design (DDD)**, **Hexagonal Architecture (Ports and Adapters)**, dan batasan lapisan (Layer Boundaries).

---

## 🏛️ Prinsip Dasar Arsitektur

Arsitektur sistem ini dibangun di atas 4 pilar utama:

1. **Domain-Centric (Framework-Agnostic)**: Inti logika bisnis (Domain) ditempatkan di dalam direktori `src/`, murni bebas dari ketergantungan framework Laravel, database, atau protokol HTTP.
2. **Hexagonal Architecture**: Pemisahan tegas antara logika bisnis dengan dunia luar menggunakan Ports (Interfaces) di domain dan Adapters (Implementations) di infrastruktur/apps.
3. **CQRS (Command Query Responsibility Segregation)**: Pemisahan total antara operasi tulis (Command -> void) dan operasi baca (Query -> DTO/ReadModel).
4. **Thin Presentation Layer**: Lapisan HTTP (`Apps/Api/`) hanya berfungsi sebagai orkestrator tipis yang memvalidasi sintaks, memetakan ke DTO, mendelegasikan ke Bus, dan memformat output.

---

## 📐 Aliran Ketergantungan Lapisan (Layer Boundaries)

Ketergantungan selalu mengarah ke dalam (menuju Domain Layer). Domain tidak pernah bergantung pada lapisan lain.

```
       ┌────────────────────────────────────────────────────────┐
       │               HTTP / Apps Layer (Apps/)                │
       │  (Controllers, FormRequests, Actions, API Resources)   │
       └───────────────────────────┬────────────────────────────┘
                                   │ memanggil (dispatches)
                                   ▼
       ┌────────────────────────────────────────────────────────┐
       │         Application Layer (src/*/Application/)         │
       │    (Commands, Queries, Handlers, Process Managers)     │
       └───────────────────────────┬────────────────────────────┘
                                   │ menggunakan (uses)
                                   ▼
       ┌────────────────────────────────────────────────────────┐
       │            Domain Layer (src/*/Domain/) ◄──────────────┤
       │ (Entities, Value Objects, Enums, Domain Events, Ports) │
       └───────────────────────────▲────────────────────────────┘
                                   │ diimplementasikan oleh
       ┌───────────────────────────┴────────────────────────────┐
       │       Infrastructure Layer (src/*/Infrastructure/)     │
       │      (Repositories, Eloquent Models, Hydrators, DB)    │
       └────────────────────────────────────────────────────────┘
```

### Ringkasan Tanggung Jawab Tiap Lapisan:

| Lapisan | Direktori | Tanggung Jawab Utama | Ketergantungan yang Diizinkan |
|---|---|---|---|
| **Domain** | `src/{BC}/Domain/` | Business rules, invariants, entities, value objects, ports (interfaces). | **TIDAK ADA** (Murni PHP, bebas framework). |
| **Application** | `src/{BC}/Application/` | Use case orchestration, Commands, Queries, Handlers, Process Managers. | Bergantung pada Domain. |
| **Infrastructure** | `src/{BC}/Infrastructure/` | Implementasi ports, akses database MySQL/Postgres, Eloquent, third-party SDKs. | Mengimplementasikan interface Domain, memanggil Laravel/DB. |
| **HTTP (Apps)** | `Apps/Api/` | HTTP routing, input syntax validation, auth access check, DTO mapping, JSON serialization. | Memanggil Application Bus (CommandBus/QueryBus), Domain DTOs/Value Objects. |

---

## 🗂️ Struktur Direktori Bounded Context

Setiap Bounded Context di dalam `src/` memiliki struktur internal seragam:

```
src/
├── {BoundedContextName}/
│   ├── Domain/
│   │   ├── Entities/          # Rich Business entities (invariants terlindungi)
│   │   ├── ValueObjects/      # Objek nilai imutabel (Ids, Money, Email, dll)
│   │   ├── Enums/             # PHP 8.1+ Enums dengan business helper ringan
│   │   ├── Events/            # Domain Events (merekam fakta masa lalu)
│   │   ├── Exceptions/        # Exception domain spesifik (bukan HTTP exception)
│   │   ├── ReadModels/        # DTO baca teroptimasi untuk query repository
│   │   ├── Services/          # Domain services (logika multi-entity)
│   │   └── Repositories/      # Port/Interface repository
│   │
│   ├── Application/
│   │   ├── Commands/          # Write operations (Command + Handler -> void)
│   │   ├── Queries/           # Read operations (Query + Handler -> DTO/ReadModel)
│   │   ├── ProcessManagers/   # Orkestrasi proses panjang / Saga / Cron
│   │   ├── Listeners/         # Event listeners untuk domain events
│   │   └── Projections/       # Pembuat proyeksi read model jika diperlukan
│   │
│   └── Infrastructure/
│       ├── Persistence/       # Repository implementations & Eloquent Models
│       ├── Hydrators/         # Mapper/Hydrator antara Model DB dan Entity Domain
│       ├── Gateways/          # Adapter sistem eksternal
│       └── Listeners/         # Infrastructure event listeners (queue, logging)
```

---

## 🌐 Transversal (Cross-Cutting) Bounded Contexts

Fungsionalitas yang digunakan bersama oleh beberapa Bounded Context diletakkan di `src/Shared/` dengan kriteria berikut:

1. **Digunakan oleh berbagai Bounded Context** (tidak spesifik domain tunggal).
2. **Menyediakan kapabilitas utilitas atau infrastruktur** (bukan business logic inti).
3. **Realisasi generic capability** (contoh: Saga Engine, Generic Audit Logging, Notification Dispatcher, Search Engine).

```
src/Shared/
├── Framework/              # Base classes (BaseEntity, Ulid, Bus interfaces)
├── Saga/                   # Long-running process orchestration
└── Audit/                  # Generic audit logging
```

---

## 🔄 Komunikasi Antar Bounded Context

### 1. Metode Utama: QueryBus dan CommandBus
Komunikasi antar Bounded Context **HARUS** melalui Application Bus:

```php
// Dari Bounded Context A, mengambil data dari Bounded Context B:
$client = $this->queryBus->query(new GetClientByIdQuery($clientId));

// Dari Bounded Context A, memicu tindakan di Bounded Context B:
$this->commandBus->dispatch(new CreateInvoiceCommand($invoiceData));
```

### 2. Aturan Penggabungan Data Antar BC (Hindari Coupling Database)
Dilarang melakukan `JOIN` SQL langsung antar tabel milik Bounded Context yang berbeda.
Lakukan penggabungan data di level Application service/PHP:

```php
// 1. Ambil invoices dari Billing BC
$invoices = $this->invoiceQuery->findByRestaurant($restaurantId);

// 2. Kumpulkan Client IDs
$clientIds = array_map(static fn($inv) => $inv->clientId, $invoices);

// 3. Ambil data client dalam 1 query via IN clause ke Client BC
$clients = $this->clientQuery->findByIds($clientIds);

// 4. Hubungkan data di PHP memory
foreach ($invoices as $invoice) {
    $invoice->client = $clients[$invoice->clientId] ?? null;
}
```

### 3. Batas Data yang Boleh Keluar dari Bounded Context

| Komponen | Boleh Keluar dari BC? | Alasan |
|---|---|---|
| **Entities** | ❌ **TIDAK BOLEH** | Entity memiliki invariant dan state yang hanya boleh dikelola oleh BC asalnya. Gunakan DTO / ReadModel. |
| **Repositories** | ❌ **TIDAK BOLEH** | Akses data internal tidak boleh diekspos ke BC lain. |
| **Domain Services** | ❌ **TIDAK BOLEH** | Logika bisnis internal harus tetap privat. |
| **Simple Value Objects** | ✅ **BOLEH** | Objek nilai sederhana yang stabil seperti `ClientId`, `RestaurantId`, `Money`. |
| **Enums** | ✅ **BOLEH** | Enumerasi nilai status umum seperti `BookingStatus`, `PaymentStatus`. |
| **ReadModels / DTOs** | ✅ **BOLEH** | Objek data transfer read-only tanpa perilaku domain. |

---

## 👥 Peran Domain Expert Sebelum Mengubah Kode BC

Setiap Bounded Context memiliki pemilik/expert (Product Owner & Tech Lead).
Aturan sebelum menyentuh atau memodifikasi Bounded Context lain:
1. Diskusikan pendekatan dan dampaknya terhadap model domain yang sudah ada.
2. Hindari membuat "jalan pintas" (shortcut) langsung ke database milik modul lain.
3. Selalu rancang perubahan menggunakan [Aggregate Design Canvas](https://github.com/ddd-crew/aggregate-design-canvas) jika menambah Aggregate baru.

---

## 🔗 Referensi Dokumen Terkait
- [02-critical-rules.md](02-critical-rules.md) - Aturan kritis yang tidak boleh dilanggar.
- [03-application-layer-cqrs.md](03-application-layer-cqrs.md) - Detail implementasi CQRS, Command, Query, dan Event.
- [04-infrastructure-layer.md](04-infrastructure-layer.md) - Detail Repository, ReadModel, Mapper, dan Eloquent.
- [presentation-layer/http-layer-patterns.md](../presentation-layer/http-layer-patterns.md) - Pola HTTP Request, Action, DTO, dan Resource.
