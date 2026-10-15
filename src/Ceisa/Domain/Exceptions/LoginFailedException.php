<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Exceptions;

use DomainException;
use Src\Shared\Framework\Domain\Exceptions\VisibleExceptionInterface;
use Throwable;

final class LoginFailedException extends DomainException implements VisibleExceptionInterface
{
    public static function invalidCredentials(): self
    {
        return new self('Login CEISA 4.0 gagal: username atau password tidak valid.');
    }

    public static function fromPrevious(Throwable $previous): self
    {
        return new self(
            sprintf('Login CEISA 4.0 gagal: %s', $previous->getMessage()),
            (int) $previous->getCode(),
            $previous,
        );
    }
}
