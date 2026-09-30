# HTTP Layer Architecture Patterns (Action-Request-Dto-Res)

Panduan standar arsitektur HTTP Layer pada **Apps/Api/**: Pola **Action**, **FormRequest**, **Input DTO**, **Custom API Resource (`XxxRes`)**, dan **Controller**.

---

## 📋 Alur Eksekusi Permintaan HTTP

```
                    HTTP Client Request (POST/GET)
                                  │
                                  ▼
      ┌────────────────────────────────────────────────────────┐
      │          1. FormRequest (Validasi Sintaks & Tipe)      │
      │  - rules(): array -> Validasi input & batasan HTTP     │
      │  - getDto(): XxxDto -> Mapping data ke Typed DTO       │
      └───────────────────────────┬────────────────────────────┘
                                  │ mengirim DTO
                                  ▼
      ┌────────────────────────────────────────────────────────┐
      │          2. Controller (Orkestrator HTTP)              │
      │  - Menerima FormRequest & Action                       │
      │  - Memanggil $action($request->getDto())               │
      │  - Menerima Resource (XxxRes)                          │
      │  - Mengembalikan response()->json($resource, status)   │
      └───────────────────────────┬────────────────────────────┘
                                  │ mengeksekusi
                                  ▼
      ┌────────────────────────────────────────────────────────┐
      │          3. Action (Thin Application Dispatcher)       │
      │  - Maksimal 20 baris kode                              │
      │  - 1. Verifikasi hak akses/keamanan (JWT)              │
      │  - 2. Dispatch Command / Query                         │
      │  - 3. Ambil dan kembalikan Resource via ResService     │
      │  - ❌ DILARANG return JsonResponse atau DTO internal    │
      └───────────────────────────┬────────────────────────────┘
                                  │
                  ┌───────────────┴───────────────┐
                  ▼                               ▼
      ┌───────────────────────┐       ┌───────────────────────┐
      │  CommandBus (Writes)  │       │   QueryBus (Reads)    │
      │  dispatch(...) -> void│       │   query(...) -> DTO   │
      └───────────────────────┘       └───────────┬───────────┘
                                                  │
                                                  ▼
      ┌───────────────────────────────────────────────────────┐
      │         4. ResService & Custom Resource (XxxRes)      │
      │  - ResService: Mengubah Domain DTO -> XxxRes          │
      │  - XxxRes: Implements JsonSerializable                │
      └───────────────────────────────────────────────────────┘
```

---

## 🗂️ Struktur Direktori `Apps/Api/`

Setiap modul di HTTP Layer diorganisasikan per *use case / action*:

```
Apps/Api/
├── Booking/                           # Bounded context / Modul
│   ├── Create/                        # Use case: Create Booking
│   │   ├── CreateBookingAction.php    # Thin action orchestrator
│   │   ├── CreateBookingRequest.php   # FormRequest (rules + getDto)
│   │   ├── CreateBookingDto.php       # Strongly-typed input DTO
│   │   └── ProductInputDto.php        # Nested input DTO (jika ada)
│   ├── Show/                          # Use case: Show Booking
│   │   ├── ShowBookingAction.php
│   │   └── ShowBookingRequest.php
│   ├── Index/                         # Use case: Index Bookings
│   │   ├── IndexBookingsAction.php
│   │   └── IndexBookingsRequest.php
│   ├── Shared/                        # Shared resources untuk modul Booking
│   │   ├── BookingRes.php             # Custom Resource (JsonSerializable)
│   │   ├── BookingCreatedRes.php      # Resource respons create
│   │   ├── BookingListItemRes.php     # Resource untuk list
│   │   └── Services/
│   │       └── BookingResService.php  # Konversi DTO -> Res
│   └── BookingController.php          # Controller delegator
└── Shared/                            # Shared lintas modul Apps
    └── Http/
        ├── AbstractFormRequest.php    # Base FormRequest dengan FormRequestHelper
        ├── FormRequestHelper.php      # Helper parsing data bertipe
        └── BaseRes.php                # Base class API Resource
```

---

## 1. Request (`rules()` + `getDto()`)

Untuk menjaga kepraktisan ekosistem Laravel sekaligus menegakkan type-safety DDD:
1. **`rules(): array`**: Digunakan untuk validasi sintaks HTTP dasar (required, min, max, format string/email).
2. **`getDto(): XxxDto`**: Memetakan input yang sudah lolos validasi ke strongly-typed DTO.

```php
namespace Apps\Api\Booking\Create;

use Apps\Shared\Http\AbstractFormRequest;
use Src\Reservation\Domain\ValueObjects\BookingId;
use Src\Reservation\Domain\ValueObjects\ClientId;
use Src\Reservation\Domain\ValueObjects\RestaurantId;

final class CreateBookingRequest extends AbstractFormRequest
{
    /**
     * 1. Validasi Sintaks HTTP (Format, Batasan Panjang, dsb)
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'string', 'size:26'],
            'restaurant_id' => ['required', 'string', 'size:26'],
            'time_slot' => ['required', 'date_format:Y-m-d H:i:s'],
            'party_size' => ['required', 'integer', 'min:1'],
            'special_requests' => ['nullable', 'string', 'max:500'],
            'products' => ['nullable', 'array'],
            'products.*.type' => ['required', 'string'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * 2. Pemetaan ke Strongly-Typed Input DTO
     */
    public function getDto(): CreateBookingDto
    {
        $helper = $this->getHelper();

        $products = array_map(
            static fn(array $item): ProductInputDto => new ProductInputDto(
                type: (string) $item['type'],
                quantity: (int) $item['quantity']
            ),
            $helper->getArray('products')
        );

        return new CreateBookingDto(
            id: BookingId::random(), // Generate ID langsung di lapisan HTTP
            clientId: ClientId::fromString($helper->getString('client_id')),
            restaurantId: RestaurantId::fromString($helper->getString('restaurant_id')),
            timeSlot: new \DateTimeImmutable($helper->getString('time_slot')),
            partySize: $helper->getInt('party_size'),
            specialRequests: $helper->getStringOrNull('special_requests'),
            products: $products,
        );
    }
}
```

---

## 2. Input DTO

```php
namespace Apps\Api\Booking\Create;

use DateTimeImmutable;
use Src\Reservation\Domain\ValueObjects\BookingId;
use Src\Reservation\Domain\ValueObjects\ClientId;
use Src\Reservation\Domain\ValueObjects\RestaurantId;

final readonly class CreateBookingDto
{
    /**
     * @param array<ProductInputDto> $products
     */
    public function __construct(
        public BookingId $id,
        public ClientId $clientId,
        public RestaurantId $restaurantId,
        public DateTimeImmutable $timeSlot,
        public int $partySize,
        public ?string $specialRequests,
        public array $products,
    ) {}
}
```

---

## 3. Thin Action (Maksimal 20 Baris)

Action adalah orchestrator pemanggilan Application Layer. Action **HANYA** memiliki 3 tanggung jawab:
1. Verifikasi hak akses (Security/JWT)
2. Dispatch Command atau Query
3. Mengembalikan Custom Resource (`XxxRes`)

### ❌ Larangan Kritis di Action:
- ❌ **Dilarang return `JsonResponse`** (Tugas Controller).
- ❌ **Dilarang return DTO internal atau raw array**.
- ❌ **Dilarang `DB::` atau akses Model**.
- ❌ **Dilarang ada loop (`foreach`, `array_map`) atau validasi bisnis**.

### ✅ Contoh Action yang Benar:

```php
namespace Apps\Api\Booking\Create;

use Apps\Api\Booking\Shared\BookingCreatedRes;
use Src\Reservation\Application\Commands\Create\CreateBookingCommand;
use Src\Shared\Framework\Infrastructure\Bus\CommandBus\CommandBusInterface;

final readonly class CreateBookingAction
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {}

    public function __invoke(CreateBookingDto $dto): BookingCreatedRes
    {
        // 1. Dispatch command (Semua business logic ada di Handler)
        $this->commandBus->dispatch(new CreateBookingCommand(
            id: $dto->id,
            clientId: $dto->clientId,
            restaurantId: $dto->restaurantId,
            timeSlot: $dto->timeSlot,
            partySize: $dto->partySize,
            specialRequests: $dto->specialRequests,
            products: array_map(
                static fn(ProductInputDto $p): array => [
                    'type' => $p->type,
                    'quantity' => $p->quantity,
                ],
                $dto->products
            ),
        ));

        // 2. Return Custom Resource (Bukan JsonResponse!)
        return new BookingCreatedRes(
            id: $dto->id->value(),
            message: 'Booking created successfully'
        );
    }
}
```

---

## 4. Custom API Resource (`XxxRes`)

Sistem menggunakan class Resource murni yang mengimplementasikan `\JsonSerializable` (atau meng-extend `BaseRes`), **bukan** `Illuminate\Http\Resources\Json\JsonResource`.

```php
namespace Apps\Api\Booking\Shared;

use Apps\Shared\Http\BaseRes;

final readonly class BookingRes extends BaseRes implements \JsonSerializable
{
    public function __construct(
        public string $id,
        public string $clientId,
        public string $restaurantId,
        public string $status,
        public string $timeSlot,
        public int $partySize,
        public ?string $specialRequests,
        public array $products = [],
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->clientId,
            'restaurant_id' => $this->restaurantId,
            'status' => $this->status,
            'time_slot' => $this->timeSlot,
            'party_size' => $this->partySize,
            'special_requests' => $this->specialRequests,
            'products' => $this->products,
        ];
    }
}
```

---

## 5. ResService (Konversi DTO -> Res)

`ResService` bertugas menjembatani domain DTO yang dikembalikan oleh QueryBus menjadi Resource HTTP:

```php
namespace Apps\Api\Booking\Shared\Services;

use Apps\Api\Booking\Shared\BookingRes;
use Src\Reservation\Application\Queries\GetBookingById\BookingDetailDto;
use Src\Reservation\Application\Queries\GetBookingById\GetBookingByIdQuery;
use Src\Reservation\Domain\ValueObjects\BookingId;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryBusInterface;

final readonly class BookingResService
{
    public function __construct(
        private QueryBusInterface $queryBus
    ) {}

    public function getBookingResource(BookingId $id): BookingRes
    {
        /** @var BookingDetailDto $dto */
        $dto = $this->queryBus->query(new GetBookingByIdQuery($id));

        return $this->fromDto($dto);
    }

    public function fromDto(BookingDetailDto $dto): BookingRes
    {
        return new BookingRes(
            id: $dto->id->value(),
            clientId: $dto->clientId->value(),
            restaurantId: $dto->restaurantId->value(),
            status: $dto->status->value,
            timeSlot: $dto->timeSlot->format('Y-m-d H:i:s'),
            partySize: $dto->partySize,
            specialRequests: $dto->specialRequests,
        );
    }
}
```

---

## 6. Controller (Orkestrator HTTP)

Controller menghubungkan Request, Action, dan konversi ke `JsonResponse`:

```php
namespace Apps\Api\Booking;

use Apps\Api\Booking\Create\CreateBookingAction;
use Apps\Api\Booking\Create\CreateBookingRequest;
use Apps\Api\Booking\Show\ShowBookingAction;
use Apps\Api\Booking\Show\ShowBookingRequest;
use Illuminate\Http\JsonResponse;

final class BookingController
{
    public function create(
        CreateBookingRequest $request,
        CreateBookingAction $action
    ): JsonResponse {
        $resource = $action($request->getDto());

        // Controller mengubah Resource menjadi JsonResponse dengan HTTP Status 201
        return response()->json($resource, 201);
    }

    public function show(
        ShowBookingRequest $request,
        ShowBookingAction $action
    ): JsonResponse {
        $resource = $action($request->getDto());

        return response()->json($resource, 200);
    }
}
```

---

## 📋 Checklist Validasi HTTP Layer

- [ ] Request class memiliki `rules(): array` untuk validasi format.
- [ ] Request class memiliki method `getDto(): XxxDto`.
- [ ] Input DTO bersifat `final readonly` dengan properti bertipe jelas.
- [ ] Action memiliki panjang **≤ 20 baris**.
- [ ] Action **TIDAK** memanggil `DB::` atau `Model::`.
- [ ] Action **TIDAK** memiliki perulangan (`foreach`, `array_map`) untuk logika bisnis.
- [ ] Action **TIDAK** mengembalikan `JsonResponse`, raw array, atau DTO.
- [ ] Action **MENGEMBALIKAN** Custom Resource (`XxxRes`).
- [ ] Controller bertugas memanggil `response()->json($resource, $status)`.
