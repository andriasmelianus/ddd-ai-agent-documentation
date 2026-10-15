<?php

declare(strict_types=1);

namespace Src\Ceisa\Application\EventListeners;

use Src\Ceisa\Domain\Events\Authenticated;
use Src\Ceisa\Domain\Repositories\AuthRepositoryInterface;

/**
 * Domain Event Listener for Authenticated event.
 *
 * Persists/stores the AuthLoginResult via AuthRepositoryInterface.
 * Note: Laravel does not have an `Illuminate\Contracts\Events\Listener` interface,
 * listeners conventionally implement `handle(Authenticated $event): void`.
 */
final readonly class StoreAuthLoginResultOnAuthenticated
{
    public function __construct(
        private AuthRepositoryInterface $authRepository,
    ) {}

    /**
     * Handle the Authenticated event.
     */
    public function handle(Authenticated $event): void
    {
        if ($event->loginResult !== null) {
            $this->authRepository->storeLoginResult($event->kodeGudang, $event->loginResult);
        }
    }
}
