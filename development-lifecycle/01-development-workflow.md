# 01 - Development Workflow (3-Phase Implementation Order)

Mandatory implementation order when creating a new feature, structured systematically across 3 incremental phases.

---

## 📋 3-Phase Execution Flow

```
PHASE 1: Domain Layer (Framework-Agnostic Business Core)
  ├── Entities (private constructor, static create, state transition methods)
  ├── Value Objects (Ids extending Ulid, Money, Email, etc.)
  ├── Enums (State, Types, Mapping)
  └── Domain Events (recording state changes)
  ✅ CONFIRMATION / UNIT TESTS before proceeding

PHASE 2: Infrastructure Layer (Persistence & Mapping)
  ├── Repository Interface (in Domain)
  ├── Eloquent Model (in Infrastructure)
  ├── Database Migration (schema, indexes, constraints)
  ├── Mapper (toDomain method using reconstitute() & toModel)
  └── Repository Implementation (in Infrastructure)
  ✅ CONFIRMATION / INTEGRATION TESTS before proceeding

PHASE 3: Application & HTTP Layer (Use Cases & Delivery)
  ├── Commands & Command Handlers (return void, publish events)
  ├── Queries, Query Handlers, & DTOs/ReadModels
  ├── FormRequest (rules() for format + getDto() for typed mapping)
  ├── Action (Thin orchestrator, returns Custom Resource XxxRes)
  ├── ResService & XxxRes (implements JsonSerializable)
  ├── Controller (response()->json(res, status))
  └── API Routes
  ✅ CONFIRMATION / END-TO-END TESTS
```

---

## 🟢 PHASE 1: Domain Layer

### 1.1 Create Value Objects & Enums First

```php
// Value Object ID
final class BookingId extends Ulid {}

// Status Enum
enum BookingStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
}
```

### 1.2 Create Domain Entity
- **`private` Constructor**.
- Provide static method `create(...)` for initial creation.
- Provide explicit methods for state transitions (never mutate status directly from outside).
- Record events using `recordLast(...)`.

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

### 2.1 Repository Interface in Domain
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

### 2.3 Mapper Using `reconstitute()`
Use the static `reconstitute()` method on the Entity (NEVER Reflection):

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

### 2.4 Repository Implementation
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
- Command Handlers **must return `void`**.
- IDs are generated prior to command dispatch.

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

## 📋 Phase Execution Checklist

### Phase 1 Checklist: Domain
- [ ] Value Objects created (e.g., ULID IDs, date formats, money).
- [ ] Status and type enums defined.
- [ ] Entities created with `private __construct`.
- [ ] `public static function create(...)` method available with domain event recording.
- [ ] Explicit state transition methods created (no public setters).
- [ ] `public static function reconstitute(...)` method available for DB hydration.

### Phase 2 Checklist: Infrastructure
- [ ] Repository interface created in `Domain/Repositories/`.
- [ ] Eloquent Model created with `$incrementing = false` & `$keyType = 'string'`.
- [ ] Database migration created complete with column types and foreign key indexes.
- [ ] Mapper created using `reconstitute()` (without Reflection).
- [ ] Repository implementation created in `Infrastructure/Persistence/`.
- [ ] Interface binding registered in Laravel Service Provider.

### Phase 3 Checklist: Application + HTTP
- [ ] Command created (`CommandInterface`), Handler returns `void`.
- [ ] Query created (`QueryInterface`), Handler returns DTO / ReadModel.
- [ ] FormRequest created with `rules()` and `getDto()` methods.
- [ ] Action created thin (≤ 20 lines) and returns Custom Resource (`XxxRes`).
- [ ] Custom Resource created implementing `JsonSerializable`.
- [ ] Controller calls `$action($request->getDto())` and wraps with `response()->json(res, status)`.
- [ ] Route registered in `routes/api.php`.
