<?php

declare(strict_types=1);

namespace Src\Ceisa\Application\Services;

use Src\Ceisa\Application\Shared\AuthLoginResultDto;
use Src\Ceisa\Domain\Events\Authenticated;
use Src\Ceisa\Domain\Exceptions\LoginFailedException;
use Src\Ceisa\Domain\Repositories\AuthRepositoryInterface;
use Src\Ceisa\Domain\Services\AuthServiceInterface;
use Src\Ceisa\Domain\ValueObjects\KodeGudang;
use Src\Shared\Framework\Infrastructure\Bus\EventBus\EventBusInterface;
use Throwable;

/**
 * Application service for managing CEISA 4.0 authentication tokens.
 *
 * Coordinates token retrieval from repository cache and triggers login
 * against CEISA 4.0 authentication service when token is missing or expired.
 */
final readonly class AuthManagerService
{
    public function __construct(
        private AuthRepositoryInterface $authRepository,
        private AuthServiceInterface $authService,
        private EventBusInterface $eventBus,
    ) {}

    /**
     * Retrieve a valid AuthLoginResultDto for CEISA 4.0 integration.
     * Checks repository cache first; performs login and publishes Authenticated event if not cached.
     */
    public function getAuthLoginResult(KodeGudang $kodeGudang): AuthLoginResultDto
    {
        $loginResult = $this->authRepository->getLoginResult($kodeGudang);

        if ($loginResult === null) {
            $credentials = $this->authRepository->getCredentials($kodeGudang);

            try {
                $loginResult = $this->authService->login($credentials);
            } catch (LoginFailedException $e) {
                throw $e;
            } catch (Throwable $e) {
                throw LoginFailedException::fromPrevious($e);
            }

            $this->eventBus->publishEvents([
                new Authenticated($kodeGudang, $credentials, $loginResult),
            ]);
        }

        return AuthLoginResultDto::fromValueObject($loginResult);
    }
}
