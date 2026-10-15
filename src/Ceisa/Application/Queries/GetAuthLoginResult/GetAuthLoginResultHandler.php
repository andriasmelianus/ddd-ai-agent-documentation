<?php

declare(strict_types=1);

namespace Src\Ceisa\Application\Queries\GetAuthLoginResult;

use Src\Ceisa\Application\Shared\AuthLoginResultDto;
use Src\Ceisa\Domain\Exceptions\AuthLoginResultNotFoundException;
use Src\Ceisa\Domain\Repositories\AuthRepositoryInterface;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryHandlerInterface;

final readonly class GetAuthLoginResultHandler implements QueryHandlerInterface
{
    public function __construct(
        private AuthRepositoryInterface $authRepository,
    ) {}

    public function __invoke(GetAuthLoginResultQuery $query): AuthLoginResultDto
    {
        $loginResult = $this->authRepository->getLoginResult($query->kodeGudang);

        if ($loginResult === null) {
            throw AuthLoginResultNotFoundException::withKodeGudang($query->kodeGudang);
        }

        return AuthLoginResultDto::fromValueObject($loginResult);
    }
}

