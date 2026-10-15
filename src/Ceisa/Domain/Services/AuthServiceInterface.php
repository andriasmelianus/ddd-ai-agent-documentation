<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Services;

use Src\Ceisa\Domain\ValueObjects\AuthCredentials;
use Src\Ceisa\Domain\ValueObjects\AuthLoginResult;

/**
 * Service interface for CEISA 4.0 authentication.
 */
interface AuthServiceInterface
{
    /**
     * Attempt login with given credentials against CEISA 4.0.
     *
     * @return AuthLoginResult Authentication result containing tokens on success.
     */
    public function login(AuthCredentials $credentials): AuthLoginResult;
}
