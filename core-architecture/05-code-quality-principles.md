# 05 - Code Quality & Domain Best Practices

Panduan standar kualitas kode: **Prinsip SOLID dalam DDD**, **Stateless Services**, **Rich Domain Entities vs Domain Services**, **Enums Berdaya Guna**, dan **Konvensi Penamaan**.

---

## 💎 Prinsip SOLID dalam Konteks DDD

### 1. Single Responsibility Principle (SRP)
**Definisi**: Sebuah class harus fokus menyelesaikan **SATU masalah bisnis**, bukan hanya memiliki satu method.

```php
// ✅ BENAR: Satu tanggung jawab (Akses data Client)
class ClientRepository implements ClientRepositoryInterface
{
    public function findById(ClientId $id): ?Client {}
    public function findByEmail(string $email): ?Client {}
    public function store(Client $client): void {}
}

// Jika class bertumbuh terlalu besar (> 500 baris):
// Pisahkan menjadi sub-tanggung jawab:
// - ClientRepository (Operasi write & aggregate core)
// - ClientRMQueryRepository (Operasi read model & analytics query)
```

### 2. Dependency Principle — "Ask Only What You Need"
**Definisi**: Sebuah method sebaiknya hanya meminta parameter yang benar-benar diperlukannya, bukan keseluruhan object besar.

```php
// ❌ SALAH: Meminta seluruh Entity Client padahal hanya butuh email
public function sendWelcomeNotification(Client $client): void
{
    $this->mailer->send($client->email(), 'Welcome!');
}

// ✅ BENAR: Hanya minta apa yang dibutuhkan
public function sendWelcomeNotification(string $email): void
{
    $this->mailer->send($email, 'Welcome!');
}
```
*Manfaat*: Sangat mudah di-unit test (tidak butuh mock Client yang rumit), dapat digunakan ulang untuk entitas lain, dan kebal dari perubahan struktur Client.

---

## ⚡ Stateless Business Services

Class Service dan Handler **DILARANG menyimpan state** di properti instance class:

```php
// ❌ SALAH: Menyimpan state (rentan race condition dan urutan pemanggilan)
class PriceCalculator
{
    private float $subtotal = 0; // STATE!

    public function addAmount(float $amount): void { $this->subtotal += $amount; }
    public function getTotal(): float { return $this->subtotal; }
}

// ✅ BENAR: Stateless Service
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

*Pengecualian*: Class report generator khusus yang memiliki **hanya satu entry point** (`public function __invoke()`) di mana state internal dibersihkan dan diinisialisasi ulang pada setiap pemanggilan.

---

## 🧠 Rich Entities vs Domain Services: Apa yang Masuk ke Mana?

```
┌───────────────────────────────────────┬───────────────────────────────────────┐
│         LOGIKA DALAM ENTITY           │      LOGIKA DALAM DOMAIN SERVICE      │
├───────────────────────────────────────┼───────────────────────────────────────┤
│ ✅ Menjaga Invariant & validasi state │ ✅ Aturan yang melibatkan BANYAK      │
│ ✅ Perhitungan internal entity        │    entitas berbeda                    │
│ ✅ Transisi status entitas            │ ✅ Validasi yang memerlukan akses DB  │
│ ✅ Mencatat domain events internal    │    (memerlukan Repository)            │
│ ❌ Dilarang inject Repository ke sini │ ✅ Koordinasi operasi antar entitas   │
└───────────────────────────────────────┴───────────────────────────────────────┘
```

### Contoh Logika dalam Entity:
```php
final class Client extends BaseEntity
{
    public function changeEmail(string $newEmail): void
    {
        // Validasi invariant
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

### Contoh Logika dalam Domain Service:
```php
final readonly class ValidateBookingAvailabilityService
{
    public function __construct(
        private BookingRepositoryInterface $bookingRepository
    ) {}

    public function isSlotAvailable(RestaurantId $restaurantId, DateTimeImmutable $timeSlot): bool
    {
        // Memerlukan akses ke repository database
        $existing = $this->bookingRepository->findByRestaurantAndSlot($restaurantId, $timeSlot);
        return $existing === null;
    }
}
```

---

## 🏷️ Pemanfaatan Enums dalam PHP 8+

Enums diizinkan memiliki behavior fungsional ringan untuk dekorasi, bisnis mini, dan translasi sistem eksternal:

### 1. Dekorasi Label & Translasi:
```php
enum BookingStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Konfirmasi',
            self::CONFIRMED => 'Terkonfirmasi',
            self::CANCELLED => 'Dibatalkan',
        };
    }
}
```

### 2. Mini Business Rules Erat:
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

### 3. Pemetaan Sistem Eksternal:
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

*Batasan*: Jangan masukkan logika kompleks atau dependensi I/O ke dalam Enum. Logika kompleks harus berada di Domain Service.

---

## ✍️ Standar Penamaan & Bahasa

1. **Konsistensi Istilah Domain**:
   - Gunakan istilah standar bahasa Inggris: `Booking` (bukan campur aduk `Reservs`, `Reservation`, `Pemesanan`).
2. **Hindari Pseudo-English ("Comprove")**:
   - ❌ Jangan gunakan `comproveBooking()` (bukan bahasa Inggris).
   - ✅ Gunakan:
     - `validateBooking()` — memeriksa aturan bisnis.
     - `checkBooking()` — memverifikasi kondisi.
     - `ensureBooking()` — memastikan kepastian.
