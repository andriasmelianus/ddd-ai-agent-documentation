# 01 - Development Workflow (3-Phase Implementation Order)

Urutan implementasi wajib saat membuat fitur baru, disusun secara terstruktur dalam 3 fase bertahap.

---

## 📋 Alur Eksekusi 3 Fase

```
PHASE 1: Domain Layer (Inti Bisnis Bebas Framework)
  ├── Entities (private constructor, static create, state transition methods)
  ├── Value Objects (Ids extends Ulid, Money, Email, dll)
  ├── Enums (State, Tipe, Mapping)
  └── Domain Events (merekam perubahan state)
  ✅ KONFIRMASI / UNIT TEST sebelum lanjut

PHASE 2: Infrastructure Layer (Persistensi & Pemetaan)
  ├── Repository Interface (di Domain)
  ├── Eloquent Model (di Infrastructure)
  ├── Database Migration (skema, index, constraints)
  ├── Mapper (metode toDomain menggunakan reconstitute() & toModel)
  └── Repository Implementation (di Infrastructure)
  ✅ KONFIRMASI / INTEGRATION TEST sebelum lanjut

PHASE 3: Application & HTTP Layer (Use Cases & Delivery)
  ├── Commands & Command Handlers (return void, publish events)
  ├── Queries, Query Handlers, & DTOs/ReadModels
  ├── FormRequest (rules() untuk format + getDto() untuk typed mapping)
  ├── Action (Thin orchestrator, return Custom Resource XxxRes)
  ├── ResService & XxxRes (implements JsonSerializable)
  ├── Controller (response()->json(res, status))
  └── Routes API
  ✅ KONFIRMASI / END-TO-END TEST
```

---

## 🟢 PHASE 1: Domain Layer

### 1.1 Buat Value Objects & Enums Terlebih Dahulu

```php
// Value Object ID
final class BookingId extends Ulid {}

// Enum Status
enum BookingStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
}
```

### 1.2 Buat Entity Domain
- **Constructor `private`**.
- Sediakan static method `create(...)` untuk inisialisasi awal.
- Sediakan method eksplisit untuk transisi state (jangan ubah status secara langsung dari luar).
- Rekam event menggunakan `recordLast(...)`.

```php
final class Booking extends BaseEntity
{
    private function __construct(
        public readonly BookingId $id,
        public readonly ClientId $clientId,
        public readonly RestaurantId $restaurantId,
        public BookingStatus $status,
        public DateTimeImmutable $timeSlot,
        public int $partySize,
    ) {}

    public static function create(
        BookingId $id,
        ClientId $clientId,
        RestaurantId $restaurantId,
        DateTimeImmutable $timeSlot,
        int $partySize,
    ): self {
        if ($partySize <= 0) {
            throw new InvalidPartySizeException("Party size must be positive");
        }

        $booking = new self(
            id: $id,
            clientId: $clientId,
            restaurantId: $restaurantId,
            status: BookingStatus::PENDING,
            timeSlot: $timeSlot,
            partySize: $partySize
        );

        $booking->recordLast(new BookingCreatedEvent($id));

        return $booking;
    }

    public function confirm(): void
    {
        if ($this->status !== BookingStatus::PENDING) {
            throw new BookingCannotBeConfirmedException("Only pending bookings can be confirmed");
        }

        $this->status = BookingStatus::CONFIRMED;
        $this->recordLast(new BookingConfirmedEvent($this->id));
    }
}
```

---

## 🟡 PHASE 2: Infrastructure Layer

### 2.1 Interface Repository di Domain
```php
interface BookingRepositoryInterface
{
    public function findById(BookingId $id): ?Booking;
    public function store(Booking $booking): void;
    public function delete(BookingId $id): void;
}
```

### 2.2 Eloquent Model & Migration
```php
// Eloquent Model
class BookingModel extends Model
{
    protected $table = 'bookings';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'client_id', 'restaurant_id', 'status', 'time_slot', 'party_size'];
}
```

### 2.3 Mapper Menggunakan `reconstitute()`
Gunakan method static `reconstitute()` di Entity (BUKAN Reflection):

```php
final readonly class BookingMapper
{
    public function toDomain(BookingModel $model): Booking
    {
        return Booking::reconstitute(
            id: BookingId::fromString($model->id),
            clientId: ClientId::fromString($model->client_id),
            restaurantId: RestaurantId::fromString($model->restaurant_id),
            status: BookingStatus::from($model->status),
            timeSlot: new DateTimeImmutable($model->time_slot),
            partySize: (int) $model->party_size
        );
    }

    public function toModel(Booking $booking): array
    {
        return [
            'id' => $booking->id->value(),
            'client_id' => $booking->clientId->value(),
            'restaurant_id' => $booking->restaurantId->value(),
            'status' => $booking->status->value,
            'time_slot' => $booking->timeSlot->format('Y-m-d H:i:s'),
            'party_size' => $booking->partySize,
        ];
    }
}
```

### 2.4 Implementasi Repository
```php
final readonly class BookingRepository implements BookingRepositoryInterface
{
    public function __construct(
        private BookingMapper $mapper
    ) {}

    public function findById(BookingId $id): ?Booking
    {
        $model = BookingModel::query()->find($id->value());
        return $model !== null ? $this->mapper->toDomain($model) : null;
    }

    public function store(Booking $booking): void
    {
        BookingModel::query()->updateOrCreate(
            ['id' => $booking->id->value()],
            $this->mapper->toModel($booking)
        );
    }

    public function delete(BookingId $id): void
    {
        BookingModel::query()->where('id', $id->value())->delete();
    }
}
```

---

## 🔵 PHASE 3: Application & HTTP Layer

### 3.1 Command & Command Handler (Write Operation)
- Command Handler **wajib return `void`**.
- ID digenerate sebelum pemanggilan command.

```php
final readonly class CreateBookingCommand implements CommandInterface
{
    public function __construct(
        public BookingId $id,
        public ClientId $clientId,
        public RestaurantId $restaurantId,
        public DateTimeImmutable $timeSlot,
        public int $partySize,
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
        $booking = Booking::create(
            id: $command->id,
            clientId: $command->clientId,
            restaurantId: $command->restaurantId,
            timeSlot: $command->timeSlot,
            partySize: $command->partySize
        );

        $this->repository->store($booking);
        $this->eventBus->publishEvents($booking->releaseEvents());
    }
}
```

### 3.2 FormRequest (`rules()` + `getDto()`)
```php
final class CreateBookingRequest extends AbstractFormRequest
{
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'string', 'size:26'],
            'restaurant_id' => ['required', 'string', 'size:26'],
            'time_slot' => ['required', 'date_format:Y-m-d H:i:s'],
            'party_size' => ['required', 'integer', 'min:1'],
        ];
    }

    public function getDto(): CreateBookingDto
    {
        $helper = $this->getHelper();

        return new CreateBookingDto(
            id: BookingId::random(),
            clientId: ClientId::fromString($helper->getString('client_id')),
            restaurantId: RestaurantId::fromString($helper->getString('restaurant_id')),
            timeSlot: new DateTimeImmutable($helper->getString('time_slot')),
            partySize: $helper->getInt('party_size'),
        );
    }
}
```

### 3.3 Thin Action & Custom Resource
```php
final readonly class CreateBookingAction
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {}

    public function __invoke(CreateBookingDto $dto): BookingCreatedRes
    {
        $this->commandBus->dispatch(new CreateBookingCommand(
            id: $dto->id,
            clientId: $dto->clientId,
            restaurantId: $dto->restaurantId,
            timeSlot: $dto->timeSlot,
            partySize: $dto->partySize
        ));

        return new BookingCreatedRes(
            id: $dto->id->value(),
            message: 'Booking created successfully'
        );
    }
}
```

### 3.4 Controller
```php
final class BookingController
{
    public function create(
        CreateBookingRequest $request,
        CreateBookingAction $action
    ): JsonResponse {
        $resource = $action($request->getDto());

        return response()->json($resource, 201);
    }
}
```

---

## 📋 Checklist Eksekusi Tiap Fase

### Checklist Fase 1: Domain
- [ ] Value Objects dibuat (misal: ID ULID, format tanggal, uang).
- [ ] Enums status dan tipe didefinisikan.
- [ ] Entity dibuat dengan `private __construct`.
- [ ] Method `public static function create(...)` tersedia dengan domain event.
- [ ] Method transisi state spesifik dibuat (tanpa setter publik).
- [ ] Method `public static function reconstitute(...)` tersedia untuk hidrasi DB.

### Checklist Fase 2: Infrastructure
- [ ] Interface Repository dibuat di `Domain/Repositories/`.
- [ ] Eloquent Model dibuat dengan `$incrementing = false` & `$keyType = 'string'`.
- [ ] Migration dibuat lengkap dengan tipe kolom dan index foreign key.
- [ ] Mapper dibuat menggunakan `reconstitute()` (tanpa Reflection).
- [ ] Implementasi Repository dibuat di `Infrastructure/Persistence/`.
- [ ] Binding Interface di Service Provider Laravel didaftarkan.

### Checklist Fase 3: Application + HTTP
- [ ] Command dibuat (`CommandInterface`), Handler return `void`.
- [ ] Query dibuat (`QueryInterface`), Handler return DTO / ReadModel.
- [ ] FormRequest dibuat dengan method `rules()` dan `getDto()`.
- [ ] Action dibuat tipis (≤ 20 baris) dan mengembalikan Custom Resource (`XxxRes`).
- [ ] Custom Resource dibuat mengimplementasikan `JsonSerializable`.
- [ ] Controller memanggil `$action($request->getDto())` dan membungkus `response()->json(res, status)`.
- [ ] Route terdaftar di `routes/api.php`.
