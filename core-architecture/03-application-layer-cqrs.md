# 03 - Application Layer & CQRS Patterns

Panduan lengkap mengenai penerapan **Command Query Responsibility Segregation (CQRS)**, **Queries**, **Commands**, **Process Managers (Saga)**, dan **Domain Events** di Application Layer.

---

## 🧭 Prinsip CQRS

```
                      ┌─────────────────────────────────────────┐
                      │              HTTP / Caller              │
                      └────────────┬────────────────────────────┘
                                   │
         ┌─────────────────────────┴─────────────────────────┐
         │                                                   │
         ▼ Writes (Commands)                                 ▼ Reads (Queries)
┌─────────────────────────────────┐                 ┌─────────────────────────────────┐
│        CommandBus               │                 │         QueryBus                │
│  dispatch(Command): void        │                 │  query(Query): DTO/ReadModel    │
└────────────────┬────────────────┘                 └────────────────┬────────────────┘
                 │                                                   │
                 ▼                                                   ▼
┌─────────────────────────────────┐                 ┌─────────────────────────────────┐
│         CommandHandler          │                 │          QueryHandler           │
│ - Ubah state entity domain      │                 │ - Ambil data teroptimasi        │
│ - Simpan via Repository         │                 │ - Return ReadModel / DTO        │
│ - Publish Domain Events         │                 │ - Stateless & Caching           │
│ - Return VOID                   │                 │ - TIDAK mengubah state          │
└─────────────────────────────────┘                 └─────────────────────────────────┘
```

---

## 📖 1. Queries (Operasi Baca)

Queries bertugas mengambil data untuk ditampilkan ke pengguna atau dikonsumsi sistem lain tanpa mengubah status sistem (*side-effect free*).

### Aturan Utama Queries:
1. **PHPDoc Wajib**: Setiap query harus mencantumkan anotasi `@see` ke Handlernya dan return type DTO-nya.
2. **Stateless Handlers**: Seluruh properti Handler wajib `readonly`. Untuk cache, gunakan sistem caching eksternal, bukan menyimpan state di properti class.
3. **Return Type yang Diizinkan**:
   - ✅ **ReadModel** langsung
   - ✅ **DTO** yang berisi Value Objects sederhana dan Enums
   - ✅ **Primitive types** (int, string, bool, float)
   - ✅ **Array/Collection** dari DTO/ReadModel
   - ❌ **DILARANG mengembalikan Entity Domain** (agar tidak bocor ke lapisan luar).

### Contoh Query & Handler:

```php
/**
 * @see GetBookingByIdHandler
 * @implements QueryInterface<BookingDetailDto>
 */
final readonly class GetBookingByIdQuery implements QueryInterface
{
    public function __construct(
        public BookingId $id
    ) {}
}

final readonly class GetBookingByIdHandler implements QueryHandlerInterface
{
    public function __construct(
        private BookingRepositoryInterface $repository
    ) {}

    public function __invoke(GetBookingByIdQuery $query): BookingDetailDto
    {
        $booking = $this->repository->findById($query->id);

        if ($booking === null) {
            throw BookingNotFoundException::withId($query->id);
        }

        return BookingDetailDto::fromEntity($booking);
    }
}
```

### Konvensi Penamaan Query

#### Verba (Kata Kerja):
- **`Get`**: Berharap data pasti ditemukan. Jika tidak ada, Handler melempar `NotFoundException`.
  - Contoh: `GetClientByIdQuery`, `GetRestaurantByIdQuery`
- **`Find`**: Data mungkin ada atau tidak ada (opsional). Mengembalikan `null` atau `array` kosong jika tidak ditemukan.
  - Contoh: `FindBookingsByDateQuery`, `FindClientByEmailQuery`
- **`Search`**: Pencarian dengan filter dinamis, paginasi, atau full-text search.
  - Contoh: `SearchClientsQuery`, `SearchBookingsQuery`

#### Ajektiva (Bentuk Output):
- **`Ref`**: Mengembalikan DTO minimal (hanya ID dan nama/field kunci).
  - Contoh: `GetClientRefQuery`
- **`Detail`**: Mengembalikan seluruh data entitas itu sendiri (tanpa relasi berat).
  - Contoh: `GetClientDetailQuery`
- **`Full`**: Mengembalikan data lengkap beserta seluruh relasi yang dibutuhkan.
  - Contoh: `GetClientFullQuery`
- **`List`**: Mengembalikan daftar data terpaginasi dan terdenormalisasi.
  - Contoh: `SearchClientListQuery`

### Aturan Penambahan Field ke Query yang Ada:
1. **Jika field berguna bagi semua konsumen & tidak butuh query tambahan**: Tambahkan langsung ke DTO yang sudah ada.
2. **Jika field hanya berguna untuk use case spesifik & membutuhkan query berat**:
   - Tambahkan flag opsional di Query (misal: `bool $withMarketingInfo = false`), ATAU
   - Buat Query baru yang spesifik (misal: `GetClientMarketingRefQuery`).

---

## ✍️ 2. Commands (Operasi Tulis)

Commands merepresentasikan niat untuk mengubah state bisnis dalam sistem.

### Aturan Utama Commands:
1. **Commands SELALU Return `void`**: Dilarang mengembalikan ID atau nilai apa pun.
2. **ID Digenerate di Awal**: Caller membuat ID (contoh: `BookingId::random()`) dan menyertakannya di parameter Command.
3. **Imperative Naming**: Nama command berupa kalimat perintah waktu sekarang (`CreateBookingCommand`, `CancelBookingCommand`, `ConfirmBookingCommand`).
4. **Hanya Memuat Data**: Command adalah Data Transfer Object murni tanpa logika bisnis.
5. **Logika Bisnis di Entity / Domain Service**: Handler hanya bertugas mengambil entity, memanggil method di entity, menyimpan via repository, dan mem-publish event.

### Contoh Command & Handler:

```php
/**
 * @see CreateBookingHandler
 */
final readonly class CreateBookingCommand implements CommandInterface
{
    /**
     * @param array<array{type: string, quantity: int}> $products
     */
    public function __construct(
        public BookingId $id,             // ID DITERUSKAN DARI CALLER
        public ClientId $clientId,
        public RestaurantId $restaurantId,
        public DateTimeImmutable $timeSlot,
        public int $partySize,
        public ?string $specialRequests,
        public array $products,
    ) {}
}

final readonly class CreateBookingHandler implements CommandHandlerInterface
{
    public function __construct(
        private BookingRepositoryInterface $repository,
        private EventBusInterface $eventBus
    ) {}

    public function __invoke(CreateBookingCommand $command): void
    {
        // 1. Eksekusi pembuatan entity via factory method
        $booking = Booking::create(
            id: $command->id,
            clientId: $command->clientId,
            restaurantId: $command->restaurantId,
            timeSlot: $command->timeSlot,
            partySize: $command->partySize,
            specialRequests: $command->specialRequests,
            products: $command->products
        );

        // 2. Simpan ke database
        $this->repository->store($booking);

        // 3. Publish domain events yang direkam oleh entity
        $this->eventBus->publishEvents($booking->releaseEvents());

        // VOID: Tidak ada return value!
    }
}
```

---

## 🔄 3. Process Managers (Saga & Long-Running Workflows)

Gunakan Process Manager jika terdapat alur proses bisnis yang melibatkan banyak command, cron background job, atau orkestrasi lintas Bounded Context.

### Karakteristik:
- Lokasi: `Application/ProcessManagers/`
- Konvensi nama: Suffix `Process` untuk command dan `ProcessHandler` untuk handler.
- **TIDAK memiliki return value** (hanya orkestrator efek samping).

```php
final readonly class SendDailyReportProcess implements CommandInterface
{
    public function __construct(
        public RestaurantId $restaurantId,
        public DateTimeImmutable $date
    ) {}
}

final readonly class SendDailyReportProcessHandler
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private CommandBusInterface $commandBus
    ) {}

    public function __invoke(SendDailyReportProcess $process): void
    {
        // 1. Ambil data melalui QueryBus
        $bookings = $this->queryBus->query(new GetBookingsByDateQuery($process->date));

        // 2. Olah laporan
        $reportData = $this->generateReport($bookings);

        // 3. Picu pengiriman email melalui CommandBus
        $this->commandBus->dispatch(new SendEmailReportCommand($process->restaurantId, $reportData));
    }
}
```

---

## 📢 4. Domain Events & Event Listeners

Domain Event merekam sesuatu yang **sudah terjadi di masa lalu** dalam domain (`BookingCreatedEvent`, `BookingConfirmedEvent`).

### Aturan Payload Event:
- ❌ **Jangan hanya menyimpan ID saja**: Konsumen akan terpaksa melakukan query berulang untuk data sederhana.
- ❌ **Jangan menyimpan seluruh Entity**: Menyebabkan tight-coupling antar event subscriber.
- ✅ **Simpan data esensial yang berubah**:
  ```php
  final readonly class BookingConfirmedEvent extends BaseDomainEvent
  {
      public function __construct(
          public BookingId $bookingId,
          public ClientId $clientId,
          public int $occurredOn
      ) {}

      public static function fromEntity(Booking $booking): self
      {
          return new self(
              $booking->id,
              $booking->clientId,
              time()
          );
      }
  }
  ```

### Eventual Consistency & Validasi Ulang:
Jika listener berjalan di latar belakang (Queue/Async), jangan mempercayai seluruh isi event untuk pengambilan keputusan kritis, karena data di database mungkin sudah berubah. Selalu baca state terkini dari repository jika memerlukan validasi konsistensi.

### Klasifikasi Listener:
- **Business Listeners** (`Application/Listeners/`): Bereaksi terhadap domain event untuk memicu aturan bisnis lain.
- **Infrastructure Listeners** (`Infrastructure/Listeners/`): Bereaksi terhadap event untuk integrasi luar, audit log, atau forwarder ke message broker (RabbitMQ/Kafka).
