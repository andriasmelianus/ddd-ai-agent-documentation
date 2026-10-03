# HTTP Layer Architecture Patterns (Action-Request-Dto-Res)

Architecture standards guide for the HTTP Layer under **Apps/Api/**: The **Action**, **FormRequest**, **Input DTO**, **Custom API Resource (`XxxRes`)**, and **Controller** pattern.

---

## 📋 HTTP Request Execution Flow

```
                    HTTP Client Request (POST/GET)
                                  │
                                  ▼
      ┌────────────────────────────────────────────────────────┐
      │       1. FormRequest (Syntax & Type Validation)        │
      │  - rules(): array -> Validates input & HTTP constraints│
      │  - getDto(): XxxDto -> Maps data to Typed DTO          │
      └───────────────────────────┬────────────────────────────┘
                                  │ sends DTO
                                  ▼
      ┌────────────────────────────────────────────────────────┐
      │          2. Controller (HTTP Orchestrator)             │
      │  - Receives FormRequest & Action                       │
      │  - Calls $action($request->getDto())                   │
      │  - Receives Resource (XxxRes)                          │
      │  - Returns response()->json($resource, status)         │
      └───────────────────────────┬────────────────────────────┘
                                  │ executes
                                  ▼
      ┌────────────────────────────────────────────────────────┐
      │       3. Action (Thin Application Dispatcher)          │
      │  - Maximum 20 lines of code                            │
      │  - 1. Verifies access control / security (JWT)         │
      │  - 2. Dispatches Command / Query                       │
      │  - 3. Fetches and returns Resource via ResService      │
      │  - ❌ FORBIDDEN to return JsonResponse or internal DTO  │
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
      │  - ResService: Converts Domain DTO -> XxxRes          │
      │  - XxxRes: Implements JsonSerializable                │
      └───────────────────────────────────────────────────────┘
```

---

## 🗂️ Directory Structure: `Apps/Api/`

Each module in the HTTP Layer is organized by *use case / action*:

```
Apps/Api/
├── Booking/                           # Bounded context / Module
│   ├── Create/                        # Use case: Create Booking
│   │   ├── CreateBookingAction.php    # Thin action orchestrator
│   │   ├── CreateBookingRequest.php   # FormRequest (rules + getDto)
│   │   ├── CreateBookingDto.php       # Strongly-typed input DTO
│   │   └── ProductInputDto.php        # Nested input DTO (if applicable)
│   ├── Show/                          # Use case: Show Booking
│   │   ├── ShowBookingAction.php
│   │   └── ShowBookingRequest.php
│   ├── Index/                         # Use case: Index Bookings
│   │   ├── IndexBookingsAction.php
│   │   └── IndexBookingsRequest.php
│   ├── Shared/                        # Shared resources for Booking module
│   │   ├── BookingRes.php             # Custom Resource (JsonSerializable)
│   │   ├── BookingCreatedRes.php      # Creation response Resource
│   │   ├── BookingListItemRes.php     # Listing Resource
│   │   └── Services/
│   │       └── BookingResService.php  # DTO -> Res conversion
│   └── BookingController.php          # Delegating Controller
└── Shared/                            # Cross-module shared HTTP components
    └── Http/
        ├── AbstractFormRequest.php    # Base FormRequest with FormRequestHelper
        ├── FormRequestHelper.php      # Helper for typed data extraction
        └── BaseRes.php                # Base class for API Resources
```

---

## 1. Request (`rules()` + `getDto()`)

To balance the practicality of the Laravel ecosystem while enforcing DDD type-safety:
1. **`rules(): array`**: Used for basic HTTP syntax validation (required, min, max, string/email format).
2. **`getDto(): XxxDto`**: Maps validated inputs into a strongly-typed DTO.

```php
namespace Apps\Api\Booking\Create;

use Apps\Shared\Http\AbstractFormRequest;
use Src\Reservation\Domain\ValueObjects\BookingId;
use Src\Reservation\Domain\ValueObjects\ClientId;
use Src\Reservation\Domain\ValueObjects\RestaurantId;

final class CreateBookingRequest extends AbstractFormRequest
{
    /**
     * 1. HTTP Syntax Validation (Format, Length Constraints, etc.)
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
     * 2. Mapping to Strongly-Typed Input DTO
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
            id: BookingId::random(), // Generate ID directly in the HTTP layer
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

## 3. Thin Action (Maximum 20 Lines)

The Action orchestrates calls to the Application Layer. An Action **ONLY** has 3 responsibilities:
1. Verify access permissions (Security/JWT)
2. Dispatch a Command or Query
3. Return a Custom Resource (`XxxRes`)

### ❌ Critical Prohibitions in Actions:
- ❌ **Forbidden to return `JsonResponse`** (Responsibility of the Controller).
- ❌ **Forbidden to return internal DTOs or raw arrays**.
- ❌ **Forbidden to use `DB::` or access Models**.
- ❌ **Forbidden to run loops (`foreach`, `array_map`) or perform business validation**.

### ✅ Example of a Proper Action:

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
        // 1. Dispatch command (All business logic resides in Handler)
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

        // 2. Return Custom Resource (Not a JsonResponse!)
        return new BookingCreatedRes(
            id: $dto->id->value(),
            message: 'Booking created successfully'
        );
    }
}
```

---

## 4. Custom API Resource (`XxxRes`)

The system utilizes pure Resource classes that implement `\JsonSerializable` (or extend `BaseRes`), **not** `Illuminate\Http\Resources\Json\JsonResource`.

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

## 5. ResService (DTO -> Res Conversion)

`ResService` bridges domain DTOs returned by the QueryBus into HTTP Resources:

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

## 6. Controller (HTTP Orchestrator)

Controllers connect Requests, Actions, and handle conversion to `JsonResponse`:

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

        // Controller converts Resource into JsonResponse with HTTP Status 201
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

## 📋 HTTP Layer Validation Checklist

- [ ] Request class contains `rules(): array` for format validation.
- [ ] Request class contains a `getDto(): XxxDto` method.
- [ ] Input DTO is `final readonly` with explicit property types.
- [ ] Action length is **≤ 20 lines**.
- [ ] Action does **NOT** call `DB::` or `Model::`.
- [ ] Action does **NOT** contain loops (`foreach`, `array_map`) for business logic.
- [ ] Action does **NOT** return `JsonResponse`, raw arrays, or DTOs.
- [ ] Action **RETURNS** a Custom Resource (`XxxRes`).
- [ ] Controller handles invoking `response()->json($resource, $status)`.
