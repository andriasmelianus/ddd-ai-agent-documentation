<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Repositories;

use Src\Ceisa\Domain\ValueObjects\AuthApiKey;
use Src\Ceisa\Domain\ValueObjects\AuthCredentials;
use Src\Ceisa\Domain\ValueObjects\AuthLoginResult;
use Src\Ceisa\Domain\ValueObjects\KodeGudang;

/**
 * Repository interface for CEISA 4.0 authentication persistence.
 */
interface AuthRepositoryInterface
{
    /**
     * Retrieve CEISA 4.0 authentication credentials for a given warehouse code.
     */
    public function getCredentials(KodeGudang $kodeGudang): AuthCredentials;

    /**
     * Retrieve CEISA 4.0 API key for a given warehouse code.
     */
    public function getApiKey(KodeGudang $kodeGudang): AuthApiKey;

    /**
     * Store the authentication login result into persistence/cache.
     */
    public function storeLoginResult(KodeGudang $kodeGudang, AuthLoginResult $result): void;

    /**
     * Retrieve the stored authentication login result from persistence/cache.
     */
    public function getLoginResult(KodeGudang $kodeGudang): ?AuthLoginResult;
}
