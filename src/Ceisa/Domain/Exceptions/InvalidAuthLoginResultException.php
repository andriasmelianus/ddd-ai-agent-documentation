<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Exceptions;

use DomainException;
use Src\Shared\Framework\Domain\Exceptions\VisibleExceptionInterface;

final class InvalidAuthLoginResultException extends DomainException implements VisibleExceptionInterface
{
    public static function emptyAccessToken(): self
    {
        return new self('Access token cannot be empty.');
    }

    public static function emptyTokenType(): self
    {
        return new self('Token type cannot be empty.');
    }

    public static function invalidExpiresIn(int $expiresIn): self
    {
        return new self(sprintf('Expires in must be greater than 0, got %d.', $expiresIn));
    }

    public static function emptyRefreshToken(): self
    {
        return new self('Refresh token cannot be empty.');
    }

    public static function invalidRefreshExpiresIn(int $refreshExpiresIn): self
    {
        return new self(sprintf('Refresh expires in must be greater than 0, got %d.', $refreshExpiresIn));
    }
}
