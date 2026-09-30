# Project Context & Living Profile

> 🔄 **LIVING DOCUMENT**: Dokumen ini BUKAN prinsip arsitektur statis. Dokumen ini **wajib dan dapat diperbarui secara berkala** oleh developer maupun AI Agent saat project berkembang (misal: penambahan Bounded Context baru, perubahan konfigurasi runtime, penambahan external integration, atau perubahan roadmap).

---

## 📌 Ringkasan Project

| Atribut | Nilai / Deskripsi |
|---|---|
| **Nama Project** | *[Isi nama project, misal: Restaurant Booking System / CRM Service]* |
| **Deskripsi Singkat** | *[Penjelasan 1-3 kalimat mengenai domain bisnis utama project ini]* |
| **Repositori / Path** | *[Path lokal atau URL repositori project]* |
| **Status Saat Ini** | Development / Staging / Production |
| **Terakhir Diperbarui** | YYYY-MM-DD |

---

## ⚙️ Lingkungan & Tech Stack

| Komponen | Spesifikasi / Konfigurasi | Catatan Khusus |
|---|---|---|
| **PHP Version** | `PHP 8.2+` (atau `PHP 8.4`) | Strict types enforced (`declare(strict_types=1);`) |
| **Framework** | `Laravel 11+` / `Laravel 12` | Domain terisolasi di `/src`, Laravel hanya sbg infrastruktur |
| **Database** | MySQL / PostgreSQL | Database besar, hindari N+1, wajib indexing kolom foreign key |
| **Runtime / Container** | Docker (Docker Compose) | Eksekusi artisan & composer wajib di dalam container |
| **Bus & Messaging** | In-Memory / RabbitMQ / Redis | CQRS CommandBus & QueryBus |
| **Static Analysis** | PHPStan (Level 8/9 / Max) | Semua property strictly typed, PHPDoc untuk generic types |
| **Testing** | PHPUnit / Pest | Unit test domain (in-memory), Integration test repo (DB test) |

---

## 🗺️ Peta Bounded Contexts & Modul

Daftar Bounded Context yang aktif di dalam project ini (di bawah direktori `src/`):

```
src/
├── [BoundedContextA]/        # Deskripsi domain A
│   ├── Domain/
│   ├── Application/
│   └── Infrastructure/
├── [BoundedContextB]/        # Deskripsi domain B
│   ├── Domain/
│   ├── Application/
│   └── Infrastructure/
└── Shared/                   # Transversal/Cross-cutting capabilities
    ├── Framework/            # BaseEntity, Ulid, Bus interfaces
    └── [CrossCuttingModule]/ # Saga, Audit, Notification (jika ada)
```

### Detail Bounded Context:

### 1. `[Nama Bounded Context 1]`
- **Tanggung Jawab Domain**: *[Apa problem domain yang diselesaikan]*
- **Aggregates & Entities Utama**: `[Entity1]`, `[Entity2]`
- **Domain Expert / PIC**: *[Nama dev / tech lead]*
- **Status Modul**: Stable / Active Development / Planned

### 2. `[Nama Bounded Context 2]`
- **Tanggung Jawab Domain**: *[Apa problem domain yang diselesaikan]*
- **Aggregates & Entities Utama**: `[Entity3]`, `[Entity4]`
- **Domain Expert / PIC**: *[Nama dev / tech lead]*
- **Status Modul**: Stable / Active Development / Planned

---

## 🌐 Integrasi Sistem Eksternal & Identifikasi

Jika project mengintegrasikan sistem luar (External Providers / Legacy Systems), catat detailnya di sini:

| Sistem Eksternal | Tujuan Integrasi | Tipe Identifikasi (`AppEnum` / External ID) | Adapter / Client Location |
|---|---|---|---|
| *[Contoh: Payment Gateway]* | Transaksi pembayaran | Menggunakan `AppComposedId` / External reference | `src/[BC]/Infrastructure/Gateways/` |
| *[Contoh: CRM / Legacy DB]* | Sinkronisasi data user | ULID Internal + AppComposedId Eksternal | `src/[BC]/Infrastructure/Adapters/` |

---

## 🚀 Perintah Standar Pengembangan (Cheat Sheet)

```bash
# Eksekusi testing di Docker
docker compose exec php php artisan test

# Eksekusi PHPStan
docker compose exec php ./vendor/bin/phpstan analyse

# Migrasi database
docker compose exec php php artisan migrate

# Format kode / Code style
docker compose exec php ./vendor/bin/pint
```

---

## 📈 Roadmap Aktif & Milestone

- [ ] **Milestone 1**: *[Deskripsi target]*
- [ ] **Milestone 2**: *[Deskripsi target]*
- [ ] **Milestone 3**: *[Deskripsi target]*

---

## 📝 Catatan Khusus & Keputusan Tim (Project Overrides)

Bagian ini digunakan untuk mencatat kesepakatan khusus tim yang berlaku spesifik pada project ini:
- *Catatan 1: [misal: Aturan pagination default adalah cursor pagination untuk endpoint feed]*
- *Catatan 2: [misal: Soft delete diwajibkan untuk semua transaksi keuangan]*
- *Catatan 3: [misal: Format ID default internal adalah ULID 26 karakter]*

---

## 🤖 Petunjuk untuk AI Agent Saat Bekerja pada Project Ini:
1. Baca dokumen ini terlebih dahulu sebelum memulai tugas baru untuk memahami konteks domain, nama modul, dan environment yang aktif.
2. Ketika kamu menambahkan Bounded Context baru atau menambah integrasi eksternal, **perbarui dokumen ini secara otomatis**.
3. Pastikan penamaan class dan namespace domain mengacu pada daftar Bounded Context di atas.
