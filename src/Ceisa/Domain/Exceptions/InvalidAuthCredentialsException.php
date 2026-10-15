<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Exceptions;

use DomainException;
use Src\Shared\Framework\Domain\Exceptions\VisibleExceptionInterface;

final class InvalidAuthCredentialsException extends DomainException implements VisibleExceptionInterface
{
    public static function passwordMatchesUsername(): self
    {
        return new self('Username and password cannot be the same.');
    }
}
