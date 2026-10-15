<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Exceptions;

use DomainException;
use Src\Shared\Framework\Domain\Exceptions\VisibleExceptionInterface;

final class InvalidAuthApiKeyException extends DomainException implements VisibleExceptionInterface
{
    public static function empty(): self
    {
        return new self('API key cannot be empty.');
    }

    public static function tooLong(int|string $valueOrMaxLength = 255, ?int $maxLength = null): self
    {
        $limit = is_int($valueOrMaxLength) ? $valueOrMaxLength : ($maxLength ?? 255);

        return new self(sprintf(
            'API key is too long. Maximum length is %d characters.',
            $limit,
        ));
    }
}

