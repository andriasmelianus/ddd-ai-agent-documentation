# 02 - Critical Rules (MUST READ)

⚠️ **NON-NEGOTIABLE RULES (MANDATORY READING BEFORE WRITING CODE)**

This document details foundational rules that must be strictly observed to prevent architectural flaws, data integrity bugs, and critical performance degradation in production environments.

---

## 🚨 TOP CRITICAL ARCHITECTURAL RULES

### 🚨 #1: Strict CQRS — Commands ALWAYS Return `void`

Write operations (Commands) in CQRS **ONLY** mutate system state and **NEVER** return a value (the return type must be `void`).

**Absolute Rules:**
- ❌ **Strictly forbidden**: Command Handlers returning IDs, Entities, booleans, or any value.
- ✅ **Key Solution**: **Generate the ID BEFORE the Command is dispatched** at the caller level (Action or caller), then pass that ID into the Command constructor.
- ✅ Command Handlers may only throw an `Exception` if a business rule fails.

```php
// ❌ FORBIDDEN: Expecting a return value from a command
$bookingId = $this->commandBus->dispatch(new CreateBookingCommand($data)); // WRONG!

// ✅ CORRECT: Generate the ID first, then dispatch the command
$bookingId = BookingId::random();

$this->commandBus->dispatch(new CreateBookingCommand(
    id: $bookingId,    // ID is passed into the command
    clientId: $clientId,
    partySize: 4
));

// Use the held $bookingId for subsequent queries or responses
```

#### Special Security/Infrastructure Exception (State-Modifying Query):
Only for pure security/infrastructure operations such as generating a JWT token or One-Time Password (OTP) where the generated value must be immediately returned to the caller and cannot be queried separately:
- **Use a Query** (not a Command) with explicit documentation in PHPDoc:
  ```php
  /**
   * @see GenerateJwtHandler
   * ⚠️ ARCHITECTURAL EXCEPTION: This query mutates system state (generates token)
   * because the token must be immediately returned to the caller.
   */
  final readonly class GenerateJwtQuery implements QueryInterface { ... }
  ```
- ❌ **Forbidden for general business operations**.

---

### 🚨 #2: Database Performance — Never Run Queries Inside Loops (N+1)

The system database is large and handles high throughput. Running SQL queries inside loops is the most fatal performance threat.

**Absolute Rules:**
- ❌ **Strictly forbidden**: Invoking database queries or repository methods inside `foreach`, `while`, or `array_map`.
- ✅ **Collect all IDs first**, then execute **ONE query** using a `WHERE IN` clause.
- ✅ Join and map data relations in PHP memory.

```php
// ❌ FORBIDDEN: N queries inside a loop
foreach ($clients as $client) {
    $bookings = $this->bookingRepository->findByClientId($client->id); // FATAL!
}

// ✅ CORRECT: 1 Query with an IN clause
// 1. Extract all IDs
$clientIds = array_map(static fn($c) => $c->id, $clients);

// 2. Fetch all bookings at once in a single query
$bookings = $this->bookingRepository->findByClientIds($clientIds);

// 3. Connect data in PHP memory
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

The Application Layer (`*Handler`) is strictly forbidden from directly touching Laravel database mechanisms (`DB::table()`, `DB::statement()`, Eloquent Models).

**Absolute Rules:**
- ❌ **Forbidden**: Using `DB::table(...)` or `Model::find(...)` inside Handlers.
- ✅ **Mandatory**: Define Repository interfaces in `Domain/Repositories/`.
- ✅ Implement those interfaces in `Infrastructure/Persistence/`.
- ✅ Inject the repository interface into the Handler constructor via dependency injection.

```php
// ❌ FORBIDDEN in Handlers:
DB::table('bookings')->where('client_id', $clientId)->get(); // WRONG!

// ✅ CORRECT:
// 1. Define in Domain
interface BookingRepositoryInterface {
    public function findByClientId(ClientId $id): array;
}

// 2. Implement in Infrastructure
final class BookingRepository implements BookingRepositoryInterface {
    public function findByClientId(ClientId $id): array {
        return DB::table('bookings')->where('client_id', $id->value())->get();
    }
}

// 3. Inject into Handler
final readonly class GetClientBookingsHandler {
    public function __construct(
        private BookingRepositoryInterface $bookingRepository
    ) {}
}
```

---

### 🚨 #4: IDs Are Value Objects, NOT Raw Strings

Within domain models and repository interfaces, never use primitive types `string` or `int` to represent Domain IDs.

**Absolute Rules:**
- ❌ **Forbidden**: `public function findById(string $id): ?Booking;`
- ✅ **Mandatory**: `public function findById(BookingId $id): ?Booking;`
- ✅ All IDs must be created as Value Objects extending base `Ulid` (or UUID).
- ✅ Conversion to string is only performed at outermost layers (e.g., in Repository implementations `$id->value()` during SQL binding).

```php
// Value Object ID
final class BookingId extends Ulid {}

// In Domain Interface:
public function findById(BookingId $id): ?Booking;

// In Infrastructure Implementation:
public function findById(BookingId $id): ?Booking {
    $row = DB::table('bookings')->where('id', $id->value())->first();
    // ...
}
```

---

### 🚨 #5: Protect Entity Invariants — Private Constructor & `reconstitute()`

Domain entities must always remain in a valid state (*protect invariants*). State must never be manipulated from the outside without validation.

**Absolute Rules:**
- ❌ **Forbidden**: Public constructors that allow creating entities in invalid states.
- ❌ **Forbidden**: Using `ReflectionClass::newInstanceWithoutConstructor()` in Mappers/Hydrators.
- ❌ **Forbidden**: Providing public setters for entity internal properties (`setStatus()`, `setItems()`).
- ✅ **Mandatory**:
  1. `private __construct(...)` — Prevents uncontrolled instantiation.
  2. `public static function create(...)` — Factory method for **NEW** data (enforces initial business invariants, sets default initial state, and records Domain Events via `recordLast(...)`).
  3. `public static function reconstitute(...)` — Factory method for **DATABASE HYDRATION** (accepts state as-is from DB without recording Domain Events).

```php
final class Booking extends BaseEntity
{
    private function __construct(
        public readonly BookingId $id,
        public readonly ClientId $clientId,
        public BookingStatus $status,
        // ...
    ) {}

    // 1. Creation of new data
    public static function create(BookingId $id, ClientId $clientId): self
    {
        $instance = new self($id, $clientId, BookingStatus::PENDING);
        $instance->recordLast(new BookingCreatedEvent($id));
        return $instance;
    }

    // 2. Reconstruction from Database
    public static function reconstitute(BookingId $id, ClientId $clientId, BookingStatus $status): self
    {
        return new self($id, $clientId, $status);
    }

    // 3. Explicit State Transition
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

The HTTP Action layer (`Apps/Api/**/Action.php`) functions strictly as a thin traffic director (maximum 20 lines).

**Absolute Rules:**
- ❌ **Forbidden**: Business logic, loops (`foreach`, `array_map`), calculations, SQL queries, or Model access inside Actions.
- ❌ **Forbidden**: Actions returning `JsonResponse` directly.
- ❌ **Forbidden**: Actions returning Application DTOs or raw arrays.
- ❌ **Forbidden**: Using Laravel's `Illuminate\Http\Resources\Json\JsonResource`.
- ✅ **Mandatory**: Actions **ALWAYS return Custom Resources (`XxxRes`)** implementing `JsonSerializable` (or extending `BaseRes`).
- ✅ **Mandatory**: The Controller layer is responsible for converting `XxxRes` into `JsonResponse` via `response()->json($resource, $status)`.

---

### 🚨 #7: FormRequest with `rules()` & `getDto()`

To blend the strengths of the Laravel ecosystem with DDD type-safety principles:
- ✅ Request classes extend `AbstractFormRequest` (or standard `FormRequest`).
- ✅ May use the `rules(): array` method for HTTP format/syntax validation (required, string, max, email, etc.).
- ✅ **Must provide a `getDto(): XxxDto` method** to extract validated data into a strongly-typed DTO ready for dispatch to the Application Layer.

---

## 🆔 Dual Identification System (Internal vs External ID)

When the system interacts across multiple systems or external applications:

### 1. Internal ID (`Ulid`)
- **Format**: ULID (26 sequential, sortable characters).
- **Purpose**: Internal primary keys, inter-table relationships, foreign keys.
- **Examples**: `BookingId`, `ClientId`, `InvoiceId`.

### 2. External Composite ID (`AppComposedId`)
- **Format**: Composed of `AppEnum $app` (system origin) + `string $id` (ID in origin system).
- **Purpose**: Prevents duplication during data synchronization from multiple external providers.
- **Examples**: `AppClientId`, `AppRestaurantId`.

---

## 🛠️ Utility / Helper Usage Rules

- Do not create new Helper classes without first checking `src/Shared/Framework/Helpers/`.
- Always leverage existing helpers:
  - `ArrayHelper`, `DateHelper`, `StringHelper`, `NumberHelper`, `MixedHelper`.
