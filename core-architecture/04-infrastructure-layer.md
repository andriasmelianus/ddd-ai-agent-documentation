# 04 - Infrastructure Layer & Persistence Patterns

Comprehensive guide to implementing the Infrastructure Layer: **Repositories**, **ReadModels**, **Table Objects**, **Entity Invariant Patterns (`reconstitute`)**, **Hydrators/Mappers**, and **Migrations**.

---

## 🏛️ Infrastructure Layer Responsibilities

The infrastructure layer resides in `src/{BoundedContext}/Infrastructure/` and is responsible for:
1. Implementing ports/interfaces defined by the Domain.
2. Interacting with databases (MySQL / PostgreSQL / Redis) using Query Builder or Eloquent.
3. Bridging physical database schemas with pure domain models via Mappers/Hydrators.
4. Handling legacy tables (*cruft*) to prevent contaminating domain purity.

---

## 🗄️ 1. Repositories

### Core Rules:
1. **Stateless**: Repositories must not maintain internal state. All dependencies (database connections, mappers) are injected through the constructor.
2. **Valid Return Types**:
   - ✅ `Entity` (for full aggregate operations)
   - ✅ `ReadModel` (for query/read operations)
   - ✅ `array<Entity>` or `array<ReadModel>`
   - ✅ Scalar values (`int`, `string`, `bool`, `float`) or `null`
   - ❌ **FORBIDDEN to return raw Query Builder results (`stdClass`, `Collection<Model>`) directly to the Application Layer.**
3. **MANDATORY CQRS Separation (Read vs Write Patterns)**:
   Persistence interfaces must ALWAYS be segregated into distinct Read and Write patterns:
   - **`*RepositoryInterface` (Write)**: Used by Command Handlers. Methods: `save(Entity $entity): void`, `delete(Id $id): void`, `findById(Id $id): ?Entity` (strictly for loading aggregate to mutate).
   - **`*QueryInterface` (Read)**: Used by Query Handlers. Methods: queries returning `ReadModel` (`*RM`), `array<ReadModel>`, or scalars. **FORBIDDEN from returning Domain Entities**.

### Interface & Implementation Example:

```php
// 1. Write Interface in Domain (src/Reservation/Domain/Repositories/BookingRepositoryInterface.php)
namespace Src\Reservation\Domain\Repositories;

use Src\Reservation\Domain\Entities\Booking;
use Src\Reservation\Domain\ValueObjects\BookingId;

interface BookingRepositoryInterface
{
    public function findById(BookingId $id): ?Booking;
    public function save(Booking $booking): void;
    public function delete(BookingId $id): void;
}

// 2. Read Interface in Domain / Application (src/Reservation/Domain/Repositories/BookingQueryInterface.php)
namespace Src\Reservation\Domain\Repositories;

use Src\Reservation\Domain\ReadModels\BookingDetailRM;
use Src\Reservation\Domain\ReadModels\BookingListItemRM;
use Src\Reservation\Domain\ValueObjects\BookingId;
use Src\Reservation\Domain\ValueObjects\ClientId;

interface BookingQueryInterface
{
    public function findById(BookingId $id): ?BookingDetailRM;
    /** @return BookingListItemRM[] */
    public function findByClientId(ClientId $clientId): array;
    public function exists(BookingId $id): bool;
}

// 2. Implementation in Infrastructure (src/Reservation/Infrastructure/Persistence/BookingRepository.php)
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

## 📊 2. ReadModels (Replacement for `array<mixed>`)

**ABSOLUTE RULE:** Returning untyped associative arrays (`array<array{...}>` or `array<mixed>`) from Repository queries is strictly forbidden. **Use ReadModels**.

### ReadModel Characteristics:
- Located in `Domain/ReadModels/`
- Names end with `RM` or `ReadModel` (e.g., `BookingListItemRM`, `ClientStatsRM`)
- Class declared `final readonly` with public properties.
- **NO business logic**.
- Specifically designed to read multi-table projections without having to hydrate full entities.

### Steps to Implement ReadModels:

#### 1. Create the ReadModel in Domain:
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

#### 2. Declare in the Repository Interface:
```php
interface BookingReadRepositoryInterface
{
    /**
     * @return array<BookingListItemRM>
     */
    public function searchBookings(BookingSearchFilters $filters): array;
}
```

#### 3. Implement in Repository:
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

To protect business invariants without impeding database hydration, entities must separate the path for new data creation from existing data reconstruction:

```php
final class Booking extends BaseEntity
{
    /** @var array<BookingProduct> */
    private array $products = [];

    // 1. Private Constructor: Prevents uncontrolled `new Booking(...)` instantiation
    private function __construct(
        public readonly BookingId $id,
        public readonly ClientId $clientId,
        public BookingStatus $status,
        public DateTimeImmutable $timeSlot,
        public int $partySize,
    ) {}

    // 2. Factory Method for NEW Data:
    // - Enforces business invariant rules
    // - Sets default initial status (e.g., PENDING)
    // - Records Domain Events
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

    // 3. Factory Method for DATABASE HYDRATION:
    // - Accepts state as-is from DB (in any status)
    // - Does NOT trigger Domain Events
    // - ONLY used in Mappers/Hydrators
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

Mappers/Hydrators are responsible for converting database row formats into Domain Entities and vice versa:

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

## 🕰️ 5. Handling Legacy Databases (Entity is Not Guilty)

If the database has an older schema with awkward columns (*cruft*):
- ❌ **Forbidden to pollute Domain Entities** with legacy fields irrelevant to modern business.
- ✅ Handle legacy fields inside the Mapper/Repository during `dehydrate()`:

```php
// In Mapper:
public function toModel(Payment $payment): array
{
    return [
        'id' => $payment->id->value(),
        'amount' => $payment->amount->value(),
        // Legacy fields handled here without polluting the domain:
        'legacy_order_id' => -1,
        'legacy_type_code' => 2,
    ];
}
```

---

## 📋 6. Table Objects (Table Constants & Column Types)

To prevent typos in table name strings across multiple files:

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

// Usage in Repository:
DB::table(BookingTable::TABLE_NAME)->where('id', $id->value())->first();
```
