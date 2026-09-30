# 04 - Infrastructure Layer & Persistence Patterns

Panduan komprehensif implementasi lapisan infrastruktur: **Repositories**, **ReadModels**, **Table Objects**, **Entity Invariant Patterns (`reconstitute`)**, **Hydrators/Mappers**, dan **Migrations**.

---

## 🏛️ Tanggung Jawab Lapisan Infrastruktur

Lapisan infrastruktur berada di `src/{BoundedContext}/Infrastructure/` dan bertugas:
1. Mengimplementasikan port/interface yang didefinisikan oleh Domain.
2. Berkomunikasi dengan database (MySQL / PostgreSQL / Redis) menggunakan Query Builder atau Eloquent.
3. Menjembatani skema database fisik dengan model domain murni melalui Mapper/Hydrator.
4. Menangani tabel warisan (*legacy tables*) agar tidak mengotori kemurnian domain.

---

## 🗄️ 1. Repositories

### Aturan Pokok:
1. **Stateless**: Repository tidak boleh menyimpan state internal. Seluruh dependensi (database connection, mapper) di-inject melalui constructor.
2. **Return Type Valid**:
   - ✅ `Entity` (untuk operasi aggregate penuh)
   - ✅ `ReadModel` (untuk operasi query/read)
   - ✅ `array<Entity>` atau `array<ReadModel>`
   - ✅ Nilai skalar (`int`, `string`, `bool`, `float`) atau `null`
   - ❌ **DILARANG mengembalikan raw Query Builder result (`stdClass`, `Collection<Model>`) langsung ke Application layer.**
3. **Pemisahan Repository Besar**: Jika sebuah Repository bertumbuh terlalu besar (> 300 baris atau banyak query kompleks), pisahkan query baca ke class terpisah:
   - `ClientRepository` (operasi write, save, delete, findById)
   - `ClientRMQueryRepository` (operasi baca agregasi, statistik, proyeksi laporan)

### Contoh Interface & Implementasi:

```php
// 1. Interface di Domain (src/Reservation/Domain/Repositories/BookingRepositoryInterface.php)
namespace Src\Reservation\Domain\Repositories;

use Src\Reservation\Domain\Entities\Booking;
use Src\Reservation\Domain\ValueObjects\BookingId;
use Src\Reservation\Domain\ValueObjects\ClientId;

interface BookingRepositoryInterface
{
    public function findById(BookingId $id): ?Booking;
    public function findByClientId(ClientId $clientId): array;
    public function store(Booking $booking): void;
    public function delete(BookingId $id): void;
}

// 2. Implementasi di Infrastructure (src/Reservation/Infrastructure/Persistence/BookingRepository.php)
namespace Src\Reservation\Infrastructure\Persistence;

use Illuminate\Support\Facades\DB;
use Src\Reservation\Domain\Entities\Booking;
use Src\Reservation\Domain\Repositories\BookingRepositoryInterface;
use Src\Reservation\Domain\ValueObjects\BookingId;
use Src\Reservation\Domain\ValueObjects\ClientId;

final readonly class BookingRepository implements BookingRepositoryInterface
{
    public function __construct(
        private BookingMapper $mapper
    ) {}

    public function findById(BookingId $id): ?Booking
    {
        $model = BookingModel::query()->with('products')->find($id->value());

        if ($model === null) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function store(Booking $booking): void
    {
        $data = $this->mapper->toModel($booking);

        BookingModel::query()->updateOrCreate(
            ['id' => $booking->id->value()],
            $data
        );
    }

    public function delete(BookingId $id): void
    {
        BookingModel::query()->where('id', $id->value())->delete();
    }
}
```

---

## 📊 2. ReadModels (Pengganti `array<mixed>`)

**ATURAN MUTLAK:** Dilarang mengembalikan associative array tak bertipe (`array<array{...}>` atau `array<mixed>`) dari Repository query. **Gunakan ReadModel**.

### Karakteristik ReadModel:
- Terletak di `Domain/ReadModels/`
- Nama berakhiran `RM` atau `ReadModel` (contoh: `BookingListItemRM`, `ClientStatsRM`)
- Class berstatus `final readonly` dengan public properties.
- **TIDAK memiliki logika bisnis**.
- Didesain spesifik untuk membaca proyeksi data dari beberapa tabel tanpa harus meng-hydrate seluruh entity.

### Langkah Penerapan ReadModel:

#### 1. Buat ReadModel di Domain:
```php
namespace Src\Reservation\Domain\ReadModels;

final readonly class BookingListItemRM
{
    public function __construct(
        public string $id,
        public string $clientName,
        public string $clientPhone,
        public string $status,
        public string $timeSlotFormatted,
        public int $partySize,
    ) {}
}
```

#### 2. Deklarasikan di Interface Repository:
```php
interface BookingReadRepositoryInterface
{
    /**
     * @return array<BookingListItemRM>
     */
    public function searchBookings(BookingSearchFilters $filters): array;
}
```

#### 3. Implementasikan di Repository:
```php
public function searchBookings(BookingSearchFilters $filters): array
{
    $results = DB::table('bookings as b')
        ->join('clients as c', 'c.id', '=', 'b.client_id')
        ->select([
            'b.id',
            'c.name as client_name',
            'c.phone as client_phone',
            'b.status',
            'b.time_slot',
            'b.party_size'
        ])
        ->get();

    return $results->map(static fn($row): BookingListItemRM => new BookingListItemRM(
        id: (string) $row->id,
        clientName: (string) $row->client_name,
        clientPhone: (string) $row->client_phone,
        status: (string) $row->status,
        timeSlotFormatted: date('Y-m-d H:i', strtotime($row->time_slot)),
        partySize: (int) $row->party_size,
    ))->all();
}
```

---

## 🧱 3. Entity Construction Pattern (`create` vs `reconstitute`)

Untuk melindungi invariant bisnis tanpa merusak kemampuan hidrasi database, entitas harus memisahkan jalur pembuatan data baru dan rekonstruksi data lama:

```php
final class Booking extends BaseEntity
{
    /** @var array<BookingProduct> */
    private array $products = [];

    // 1. Private Constructor: Mencegah new Booking(...) sembarangan
    private function __construct(
        public readonly BookingId $id,
        public readonly ClientId $clientId,
        public BookingStatus $status,
        public DateTimeImmutable $timeSlot,
        public int $partySize,
    ) {}

    // 2. Factory Method untuk Data BARU:
    // - Menegakkan aturan invariant bisnis
    // - Status awal default (misal: PENDING)
    // - Merekam Domain Event
    public static function create(
        BookingId $id,
        ClientId $clientId,
        DateTimeImmutable $timeSlot,
        int $partySize,
    ): self {
        if ($partySize <= 0) {
            throw new InvalidPartySizeException("Party size must be greater than zero");
        }

        $booking = new self(
            id: $id,
            clientId: $clientId,
            status: BookingStatus::PENDING,
            timeSlot: $timeSlot,
            partySize: $partySize
        );

        $booking->recordLast(new BookingCreatedEvent($id));

        return $booking;
    }

    // 3. Factory Method untuk HIDRASI DARI DATABASE:
    // - Menerima state apa adanya dari DB (bisa status apa pun)
    // - TIDAK memicu Domain Event
    // - HANYA digunakan di Mapper/Hydrator
    public static function reconstitute(
        BookingId $id,
        ClientId $clientId,
        BookingStatus $status,
        DateTimeImmutable $timeSlot,
        int $partySize,
        array $products = [],
    ): self {
        $booking = new self(
            id: $id,
            clientId: $clientId,
            status: $status,
            timeSlot: $timeSlot,
            partySize: $partySize
        );

        $booking->products = $products;
        $booking->setIsNew(false);

        return $booking;
    }
}
```

---

## 🔄 4. Hydrators & Mappers

Mapper/Hydrator bertanggung jawab mengubah format baris database ke Domain Entity dan sebaliknya:

```php
final readonly class BookingMapper
{
    public function toDomain(BookingModel $model): Booking
    {
        $bookingId = BookingId::fromString($model->id);

        $products = [];
        if ($model->relationLoaded('products')) {
            foreach ($model->products as $productModel) {
                $products[] = new BookingProduct(
                    type: $productModel->type,
                    quantity: (int) $productModel->quantity
                );
            }
        }

        return Booking::reconstitute(
            id: $bookingId,
            clientId: ClientId::fromString($model->client_id),
            status: BookingStatus::from($model->status),
            timeSlot: new DateTimeImmutable($model->time_slot),
            partySize: (int) $model->party_size,
            products: $products
        );
    }

    public function toModel(Booking $booking): array
    {
        return [
            'id' => $booking->id->value(),
            'client_id' => $booking->clientId->value(),
            'status' => $booking->status->value,
            'time_slot' => $booking->timeSlot->format('Y-m-d H:i:s'),
            'party_size' => $booking->partySize,
        ];
    }
}
```

---

## 🕰️ 5. Menangani Legacy Database (Entity is Not Guilty)

Jika database memiliki skema lama dengan kolom janggal (*cruft*):
- ❌ **Dilarang mencemari Entity Domain** dengan field-field warisan yang tidak relevan dengan bisnis modern.
- ✅ Simpan field warisan tersebut di Mapper/Repository `dehydrate()`:

```php
// Di Mapper:
public function toModel(Payment $payment): array
{
    return [
        'id' => $payment->id->value(),
        'amount' => $payment->amount->value(),
        // Field legacy ditangani di sini tanpa mengotori domain:
        'legacy_order_id' => -1,
        'legacy_type_code' => 2,
    ];
}
```

---

## 📋 6. Table Objects (Konstanta Tabel & Tipe Kolom)

Untuk mencegah kesalahan pengetikan nama tabel string di berbagai tempat:

```php
/**
 * @property string $id
 * @property string $client_id
 * @property string $status
 * @property string $time_slot
 * @property int $party_size
 */
final class BookingTable
{
    public const TABLE_NAME = 'bookings';
}

// Penggunaan di Repository:
DB::table(BookingTable::TABLE_NAME)->where('id', $id->value())->first();
```
