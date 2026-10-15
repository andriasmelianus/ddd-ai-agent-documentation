# 03 - Application Layer & CQRS Patterns

Comprehensive guide to applying **Command Query Responsibility Segregation (CQRS)**, **Queries**, **Commands**, **Process Managers (Sagas)**, and **Domain Events** in the Application Layer.

---

## 🧭 CQRS Principles

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
│ - Mutate domain entity state    │                 │ - Fetch optimized data          │
│ - Persist via Repository        │                 │ - Return ReadModel / DTO        │
│ - Publish Domain Events         │                 │ - Stateless & Caching           │
│ - Return VOID                   │                 │ - Does NOT mutate state         │
└─────────────────────────────────┘                 └─────────────────────────────────┘
```

---

## 📖 1. Queries (Read Operations)

Queries are responsible for fetching data to display to users or consume in other systems without mutating system state (*side-effect free*).

### Core Rules for Queries:
1. **Mandatory PHPDoc**: Every query must include an `@see` annotation linking to its Handler and specifying its return type DTO.
2. **Stateless Handlers**: All Handler properties must be `readonly`. For caching, use external cache systems rather than storing state in class properties.
3. **Permitted Return Types**:
   - ✅ **ReadModel** directly
   - ✅ **DTO** composed of simple Value Objects and Enums
   - ✅ **Primitive types** (int, string, bool, float)
   - ✅ **Array/Collection** of DTOs/ReadModels
   - ❌ **FORBIDDEN to return Domain Entities** (to prevent leakage to outer layers).

### Example Query & Handler:

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

### Query Naming Conventions

#### Verbs:
- **`Get`**: Expects data to strictly exist. If not found, Handler throws `NotFoundException`.
  - Examples: `GetClientByIdQuery`, `GetRestaurantByIdQuery`
- **`Find`**: Data may or may not exist (optional). Returns `null` or an empty `array` when not found.
  - Examples: `FindBookingsByDateQuery`, `FindClientByEmailQuery`
- **`Search`**: Search queries with dynamic filters, pagination, or full-text search.
  - Examples: `SearchClientsQuery`, `SearchBookingsQuery`

#### Adjectives (Output Forms):
- **`Ref`**: Returns a minimal DTO (only ID and name/key fields).
  - Example: `GetClientRefQuery`
- **`Detail`**: Returns all data of the entity itself (without heavy relationships).
  - Example: `GetClientDetailQuery`
- **`Full`**: Returns complete data including all required relations.
  - Example: `GetClientFullQuery`
- **`List`**: Returns a paginated, denormalized list of records.
  - Example: `SearchClientListQuery`

### Rules for Adding Fields to Existing Queries:
1. **If the field is useful to all consumers & requires no extra query**: Add it directly to the existing DTO.
2. **If the field is only needed for a specific use case & requires a heavy query**:
   - Add an optional flag to the Query (e.g., `bool $withMarketingInfo = false`), OR
   - Create a dedicated new Query (e.g., `GetClientMarketingRefQuery`).

---

## ✍️ 2. Commands (Write Operations)

Commands represent an intent to change business state within the system.

### Core Rules for Commands:
1. **Commands ALWAYS Return `void`**: Forbidden from returning IDs or any value.
2. **IDs Generated Upfront**: Caller creates the ID (e.g., `BookingId::random()`) and passes it in the Command parameters.
3. **Imperative Naming**: Command names use present-tense imperative verbs (`CreateBookingCommand`, `CancelBookingCommand`, `ConfirmBookingCommand`).
4. **Data-Only**: Commands are pure Data Transfer Objects without business logic.
5. **Business Logic in Entity / Domain Service**: The Handler only retrieves the entity, invokes methods on the entity, saves via the repository, and publishes events.

### Example Command & Handler:

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
        public BookingId $id,             // ID PASSED IN FROM CALLER
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
        // 1. Execute entity creation via factory method
        $booking = Booking::create(
            id: $command->id,
            clientId: $command->clientId,
            restaurantId: $command->restaurantId,
            timeSlot: $command->timeSlot,
            partySize: $command->partySize,
            specialRequests: $command->specialRequests,
            products: $command->products
        );

        // 2. Persist to database
        $this->repository->store($booking);

        // 3. Publish domain events recorded by entity
        $this->eventBus->publishEvents($booking->releaseEvents());

        // VOID: No return value!
    }
}
```

---

## 🔄 3. Process Managers (Sagas & Long-Running Workflows)

Use Process Managers when a business workflow spans multiple commands, cron background jobs, or orchestrates across multiple Bounded Contexts.

### Characteristics:
- Location: `Application/ProcessManagers/`
- Naming convention: Suffix `Process` for command and `ProcessHandler` for handler.
- **NO return value** (solely orchestrates side-effects).

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
        // 1. Fetch data via QueryBus
        $bookings = $this->queryBus->query(new GetBookingsByDateQuery($process->date));

        // 2. Process report
        $reportData = $this->generateReport($bookings);

        // 3. Trigger email dispatch via CommandBus
        $this->commandBus->dispatch(new SendEmailReportCommand($process->restaurantId, $reportData));
    }
}
```

---

## 📢 4. Domain Events & Event Listeners

A Domain Event records something that has **already happened in the past** within the domain (`BookingCreatedEvent`, `BookingConfirmedEvent`).

### Event Payload Rules:
- ❌ **Do not store only IDs**: Consumers would be forced into repeated queries for simple data.
- ❌ **Do not store the entire Entity**: Causes tight coupling between event subscribers.
- ✅ **Store essential mutated data**:
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

### Eventual Consistency & Re-Validation:
When a listener runs in the background (Queue/Async), never blindly trust the entire event payload for critical decisions, as database data may have changed. Always re-fetch the latest state from the repository when consistency validation is required.

### Listener Classification:
- **Business Listeners** (`Application/Listeners/`): React to domain events to trigger downstream business rules.
- **Infrastructure Listeners** (`Infrastructure/Listeners/`): React to events for external integration, audit logging, or forwarding to message brokers (RabbitMQ/Kafka).
