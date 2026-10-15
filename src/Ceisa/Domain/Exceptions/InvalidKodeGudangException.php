<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Exceptions;

use DomainException;
use Src\Shared\Framework\Domain\Exceptions\VisibleExceptionInterface;

final class InvalidKodeGudangException extends DomainException implements VisibleExceptionInterface
{
    public static function empty(): self
    {
        return new self('Kode gudang cannot be empty.');
    }

    public static function tooShort(string $value, int $minLength): self
    {
        return new self(sprintf(
            'Kode gudang "%s" is too short. Minimum length is %d characters.',
            $value,
            $minLength,
        ));
    }

    public static function tooLong(string $value, int $maxLength): self
    {
        return new self(sprintf(
            'Kode gudang "%s" is too long. Maximum length is %d characters.',
            $value,
            $maxLength,
        ));
    }

    public static function invalidCharacters(string $value): self
    {
        return new self(sprintf(
            'Kode gudang "%s" contains invalid characters. Only letters and numbers are allowed.',
            $value,
        ));
    }
}

class_alias(InvalidKodeGudangException::class, 'Src\Ceisa\Domain\Exceptions\InvalidWarehouseCodeException');

