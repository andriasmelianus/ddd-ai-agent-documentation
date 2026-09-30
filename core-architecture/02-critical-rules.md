# 02 - Critical Rules (MUST READ)

⚠️ **ATURAN NON-NEGOTIABLE (WAJIB DIBACA SEBELUM MENULIS KODE)**

Dokumen ini memuat aturan-aturan fundamental yang mutlak ditaati untuk mencegah cacat arsitektur, bug integritas data, dan masalah performa kritis di lingkungan produksi.

---

## 🚨 TOP CRITICAL ARCHITECTURAL RULES

### 🚨 #1: Strict CQRS — Commands ALWAYS Return `void`

Operasi penulisan (Command) dalam CQRS **HANYA** bertugas mengubah status sistem dan **TIDAK PERNAH** mengembalikan nilai (return type wajib `void`).

**Aturan Mutlak:**
- ❌ **Dilarang keras**: Command Handler mengembalikan ID, Entity, boolean, atau nilai apa pun.
- ✅ **Kunci Solusi**: **Generate ID SEBELUM Command di-dispatch** di lapisan pemanggil (Action atau caller), lalu kirimkan ID tersebut ke dalam constructor Command.
- ✅ Command Handler hanya boleh melempar `Exception` jika terjadi kegagalan aturan bisnis.

```php
// ❌ FORBIDDEN: Mengharapkan return value dari command
$bookingId = $this->commandBus->dispatch(new CreateBookingCommand($data)); // SALAH!

// ✅ CORRECT: Generate ID terlebih dahulu, baru dispatch command
$bookingId = BookingId::random();

$this->commandBus->dispatch(new CreateBookingCommand(
    id: $bookingId,    // ID diteruskan ke dalam command
    clientId: $clientId,
    partySize: 4
));

// Gunakan $bookingId yang sudah dipegang untuk query lanjutan atau response
```

#### Pengecualian Khusus Keamanan/Infrastruktur (State-Modifying Query):
Hanya untuk operasi keamanan/infrastruktur murni seperti pembuatan token JWT atau One-Time Password (OTP) di mana nilai yang dibuat harus segera dikembalikan ke pemanggil dan tidak bisa di-query terpisah:
- **Gunakan Query** (bukan Command) dengan dokumentasi eksplisit di PHPDoc:
  ```php
  /**
   * @see GenerateJwtHandler
   * ⚠️ ARCHITECTURAL EXCEPTION: Query ini memodifikasi status sistem (generate token)
   * karena token harus dikembalikan seketika ke caller.
   */
  final readonly class GenerateJwtQuery implements QueryInterface { ... }
  ```
- ❌ **Dilarang untuk operasi bisnis umum**.

---

### 🚨 #2: Database Performance — Dilarang Menjalankan Query di Dalam Loop (N+1)

Database sistem berukuran besar dan melayani throughput tinggi. Query SQL di dalam loop merupakan ancaman performa paling fatal.

**Aturan Mutlak:**
- ❌ **Dilarang keras**: Memanggil query database atau repository method di dalam `foreach`, `while`, atau `array_map`.
- ✅ **Kumpulkan seluruh ID terlebih dahulu**, lalu jalankan **SATU query** menggunakan klausa `WHERE IN`.
- ✅ Hubungkan relasi data di memori PHP.

```php
// ❌ FORBIDDEN: N Query di dalam loop
foreach ($clients as $client) {
    $bookings = $this->bookingRepository->findByClientId($client->id); // FATAL!
}

// ✅ CORRECT: 1 Query dengan IN clause
// 1. Ekstrak seluruh ID
$clientIds = array_map(static fn($c) => $c->id, $clients);

// 2. Ambil seluruh booking sekaligus dalam 1 query
$bookings = $this->bookingRepository->findByClientIds($clientIds);

// 3. Hubungkan data di memori PHP
$bookingsByClient = [];
foreach ($bookings as $booking) {
    $bookingsByClient[$booking->clientId->value()][] = $booking;
}

foreach ($clients as $client) {
    $client->bookings = $bookingsByClient[$client->id->value()] ?? [];
}
```

---

### 🚨 #3: Handlers NEVER Use `DB::` Directly — Always Use Repositories

Lapisan Application (`*Handler`) dilarang keras menyentuh database Laravel (`DB::table()`, `DB::statement()`, Eloquent Model) secara langsung.

**Aturan Mutlak:**
- ❌ **Dilarang**: Menggunakan `DB::table(...)` atau `Model::find(...)` di dalam Handler.
- ✅ **Wajib**: Definisikan interface Repository di `Domain/Repositories/`.
- ✅ Implementasikan interface tersebut di `Infrastructure/Persistence/`.
- ✅ Inject interface repository ke constructor Handler via dependency injection.

```php
// ❌ FORBIDDEN in Handlers:
DB::table('bookings')->where('client_id', $clientId)->get(); // SALAH!

// ✅ CORRECT:
// 1. Definisikan di Domain
interface BookingRepositoryInterface {
    public function findByClientId(ClientId $id): array;
}

// 2. Implementasikan di Infrastructure
final class BookingRepository implements BookingRepositoryInterface {
    public function findByClientId(ClientId $id): array {
        return DB::table('bookings')->where('client_id', $id->value())->get();
    }
}

// 3. Inject ke Handler
final readonly class GetClientBookingsHandler {
    public function __construct(
        private BookingRepositoryInterface $bookingRepository
    ) {}
}
```

---

### 🚨 #4: IDs Are Value Objects, NOT Raw Strings

Dalam model domain dan interface repository, dilarang menggunakan tipe data primitif `string` atau `int` untuk merepresentasikan Domain ID.

**Aturan Mutlak:**
- ❌ **Dilarang**: `public function findById(string $id): ?Booking;`
- ✅ **Wajib**: `public function findById(BookingId $id): ?Booking;`
- ✅ Seluruh ID dibuat sebagai Value Object yang meng-extend base `Ulid` (atau UUID).
- ✅ Konversi ke string hanya dilakukan di lapisan terluar (misal: di implementasi Repository `$id->value()` saat binding SQL).

```php
// Value Object ID
final class BookingId extends Ulid {}

// Di Interface Domain:
public function findById(BookingId $id): ?Booking;

// Di Implementasi Infrastructure:
public function findById(BookingId $id): ?Booking {
    $row = DB::table('bookings')->where('id', $id->value())->first();
    // ...
}
```

---

### 🚨 #5: Proteksi Invariant Entity — Private Constructor & `reconstitute()`

Entity domain harus selalu berada dalam kondisi valid (*protect invariants*). State tidak boleh dimanipulasi dari luar tanpa validasi.

**Aturan Mutlak:**
- ❌ **Dilarang**: Constructor publik yang memungkinkan pembuatan entitas dalam kondisi tidak valid.
- ❌ **Dilarang**: Menggunakan `ReflectionClass::newInstanceWithoutConstructor()` di Mapper/Hydrator.
- ❌ **Dilarang**: Menyediakan public setter untuk properti internal entity (`setStatus()`, `setItems()`).
- ✅ **Wajib**:
  1. `private __construct(...)` — Mencegah inisialisasi liar.
  2. `public static function create(...)` — Factory method untuk data **BARU** (menjalankan validasi aturan awal, menetapkan default initial state, dan merekam Domain Event `recordLast(...)`).
  3. `public static function reconstitute(...)` — Factory method untuk **HIDRASI DATABASE** (menerima state apa adanya dari DB tanpa merekam Domain Event).

```php
final class Booking extends BaseEntity
{
    private function __construct(
        public readonly BookingId $id,
        public readonly ClientId $clientId,
        public BookingStatus $status,
        // ...
    ) {}

    // 1. Pembuatan data baru
    public static function create(BookingId $id, ClientId $clientId): self
    {
        $instance = new self($id, $clientId, BookingStatus::PENDING);
        $instance->recordLast(new BookingCreatedEvent($id));
        return $instance;
    }

    // 2. Rekonstruksi dari Database
    public static function reconstitute(BookingId $id, ClientId $clientId, BookingStatus $status): self
    {
        return new self($id, $clientId, $status);
    }

    // 3. Transisi State eksplisit
    public function confirm(): void
    {
        if ($this->status !== BookingStatus::PENDING) {
            throw new BookingCannotBeConfirmedException("Status must be pending");
        }
        $this->status = BookingStatus::CONFIRMED;
        $this->recordLast(new BookingConfirmedEvent($this->id));
    }
}
```

---

### 🚨 #6: Thin Actions & Custom API Resources

Lapisan HTTP Action (`Apps/Api/**/Action.php`) hanya berfungsi sebagai pemandu lalu lintas tipis (maksimal 20 baris).

**Aturan Mutlak:**
- ❌ **Dilarang**: Logika bisnis, loop (`foreach`, `array_map`), perhitungan, query SQL, atau akses Model di dalam Action.
- ❌ **Dilarang**: Action mengembalikan `JsonResponse` secara langsung.
- ❌ **Dilarang**: Action mengembalikan Application DTO atau raw array.
- ❌ **Dilarang**: Menggunakan Laravel `Illuminate\Http\Resources\Json\JsonResource`.
- ✅ **Wajib**: Action **SELALU mengembalikan Custom Resource (`XxxRes`)** yang mengimplementasikan `JsonSerializable` (atau meng-extend `BaseRes`).
- ✅ **Wajib**: Lapisan Controller bertugas mengubah `XxxRes` menjadi `JsonResponse` dengan `response()->json($resource, $status)`.

---

### 🚨 #7: FormRequest dengan `rules()` & `getDto()`

Untuk memadukan kekuatan ekosistem Laravel dengan prinsip type-safety DDD:
- ✅ Request class meng-extend `AbstractFormRequest` (atau standard `FormRequest`).
- ✅ Boleh menggunakan method `rules(): array` untuk validasi format/sintaks HTTP (required, string, max, email, dll).
- ✅ **Wajib menyediakan method `getDto(): XxxDto`** untuk mengekstrak data tervalidasi ke dalam strongly-typed DTO yang siap dikirimkan ke lapisan Application.

---

## 🆔 Dual Identification System (Internal vs External ID)

Jika sistem berinteraksi dengan multi-sistem atau aplikasi eksternal:

### 1. Internal ID (`Ulid`)
- **Format**: ULID (26 karakter acak berurutan).
- **Tujuan**: Primary key internal, relasi antar tabel, foreign key.
- **Contoh**: `BookingId`, `ClientId`, `InvoiceId`.

### 2. External Composite ID (`AppComposedId`)
- **Format**: Terdiri dari `AppEnum $app` (asal sistem) + `string $id` (ID di sistem asal).
- **Tujuan**: Menghindari duplikasi saat sinkronisasi data dari berbagai provider luar.
- **Contoh**: `AppClientId`, `AppRestaurantId`.

---

## 🛠️ Aturan Penggunaan Utility / Helper

- Dilarang membuat class Helper baru tanpa memeriksa `src/Shared/Framework/Helpers/` terlebih dahulu.
- Selalu manfaatkan helper yang sudah tersedia:
  - `ArrayHelper`, `DateHelper`, `StringHelper`, `NumberHelper`, `MixedHelper`.
