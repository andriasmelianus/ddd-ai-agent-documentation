# 05 - Code Quality & Domain Best Practices

Code quality standards guide: **SOLID Principles in DDD**, **Stateless Services**, **Rich Domain Entities vs Domain Services**, **Expressive Enums**, and **Naming Conventions**.

---

## 💎 SOLID Principles in the Context of DDD

### 1. Single Responsibility Principle (SRP)
**Definition**: A class must focus on solving **ONE business problem**, not merely contain a single method.

```php
// ✅ CORRECT: Single responsibility (Client data access)
class ClientRepository implements ClientRepositoryInterface
{
    public function findById(ClientId $id): ?Client {}
    public function findByEmail(string $email): ?Client {}
    public function store(Client $client): void {}
}

// If a class grows excessively large (> 500 lines):
// Split into sub-responsibilities:
// - ClientRepository (Core aggregate & write operations)
// - ClientRMQueryRepository (Read model & analytics query operations)
```

### 2. Dependency Principle — "Ask Only What You Need"
**Definition**: A method should only request parameters it genuinely needs, rather than requiring an entire large object.

```php
// ❌ WRONG: Requesting the entire Client Entity when only the email is needed
public function sendWelcomeNotification(Client $client): void
{
    $this->mailer->send($client->email(), 'Welcome!');
}

// ✅ CORRECT: Ask only for what is needed
public function sendWelcomeNotification(string $email): void
{
    $this->mailer->send($email, 'Welcome!');
}
```
*Benefits*: Trivial to unit test (no cumbersome Client mocks required), reusable across other entities, and immune to structural changes in the Client entity.

---

## ⚡ Stateless Business Services

Service classes and Handlers are **STRICTLY FORBIDDEN from storing state** in instance properties:

```php
// ❌ WRONG: Storing state (susceptible to race conditions and execution ordering bugs)
class PriceCalculator
{
    private float $subtotal = 0; // STATE!

    public function addAmount(float $amount): void { $this->subtotal += $amount; }
    public function getTotal(): float { return $this->subtotal; }
}

// ✅ CORRECT: Stateless Service
final readonly class PriceCalculator
{
    public function __construct(
        private TaxService $taxService
    ) {}

    public function calculate(Booking $booking): Money
    {
        return $this->taxService->applyTax($booking->basePrice());
    }
}
```

*Exception*: Dedicated report generator classes that have **strictly one entry point** (`public function __invoke()`) where internal state is wiped and reinitialized on each invocation.

---

## 🧠 Rich Entities vs Domain Services: What Goes Where?

```
┌───────────────────────────────────────┬───────────────────────────────────────┐
│          LOGIC WITHIN ENTITY          │      LOGIC WITHIN DOMAIN SERVICE      │
├───────────────────────────────────────┼───────────────────────────────────────┤
│ ✅ Enforcing Invariants & state       │ ✅ Rules involving MULTIPLE distinct  │
│    validation                         │    entities                           │
│ ✅ Internal entity calculations       │ ✅ Validations requiring DB access    │
│ ✅ Entity status transitions          │    (requires Repository)              │
│ ✅ Recording internal domain events   │ ✅ Coordinating operations across     │
│ ❌ Never inject Repositories here     │    entities                           │
└───────────────────────────────────────┴───────────────────────────────────────┘
```

### Example of Logic within an Entity:
```php
final class Client extends BaseEntity
{
    public function changeEmail(string $newEmail): void
    {
        // Invariant validation
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException($newEmail);
        }

        $this->email = $newEmail;
        $this->recordLast(new ClientEmailChangedEvent($this->id, $newEmail));
    }

    public function isVip(): bool
    {
        return $this->type === ClientType::VIP;
    }
}
```

### Example of Logic within a Domain Service:
```php
final readonly class ValidateBookingAvailabilityService
{
    public function __construct(
        private BookingRepositoryInterface $bookingRepository
    ) {}

    public function isSlotAvailable(RestaurantId $restaurantId, DateTimeImmutable $timeSlot): bool
    {
        // Requires database repository access
        $existing = $this->bookingRepository->findByRestaurantAndSlot($restaurantId, $timeSlot);
        return $existing === null;
    }
}
```

---

## 🏷️ Expressive Enums in PHP 8+

Enums are permitted to contain lightweight functional behaviors for presentation labels, tightly coupled mini-rules, and external system translations:

### 1. Label Decoration & Translation:
```php
enum BookingStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending Confirmation',
            self::CONFIRMED => 'Confirmed',
            self::CANCELLED => 'Cancelled',
        };
    }
}
```

### 2. Tightly Coupled Mini Business Rules:
```php
enum ClientTier: string
{
    case REGULAR = 'regular';
    case VIP = 'vip';
    case PLATINUM = 'platinum';

    public function discountPercentage(): int
    {
        return match($this) {
            self::PLATINUM => 25,
            self::VIP => 15,
            self::REGULAR => 0,
        };
    }
}
```

### 3. External System Mapping:
```php
enum ExternalPaymentGatewayStatus: string
{
    case AUTHORIZED = 'authorized';
    case FAILED = 'failed';

    public function toDomainStatus(): PaymentStatus
    {
        return match($this) {
            self::AUTHORIZED => PaymentStatus::PAID,
            self::FAILED => PaymentStatus::FAILED,
        };
    }
}
```

*Constraint*: Do not embed complex logic or I/O dependencies inside Enums. Complex logic belongs in Domain Services.

---

## ✍️ Naming Standards & Ubiquitous Language

1. **Ubiquitous Domain Terminology**:
   - Use standard English terms consistently: `Booking` (avoid mixing `Reservs`, `Reservation`, `Pemesanan`).
2. **Avoid Pseudo-English ("Comprove")**:
   - ❌ Do not use `comproveBooking()` (not standard English).
   - ✅ Use:
     - `validateBooking()` — verify business rules.
     - `checkBooking()` — inspect conditions.
     - `ensureBooking()` — guarantee invariants.
