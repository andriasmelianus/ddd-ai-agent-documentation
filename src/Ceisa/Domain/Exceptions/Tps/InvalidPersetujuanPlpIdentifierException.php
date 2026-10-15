<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Exceptions\Tps;

use InvalidArgumentException;
use Src\Shared\Framework\Domain\Exceptions\VisibleExceptionInterface;

final class InvalidPersetujuanPlpIdentifierException extends InvalidArgumentException implements VisibleExceptionInterface
{
    public static function emptyNomorPlp(): self
    {
        return new self('PLP number cannot be empty.');
    }

    public static function nomorPlpTooLong(string $number, int $max): self
    {
        return new self(sprintf('PLP number "%s" exceeds maximum length of %d characters.', $number, $max));
    }

    public static function emptyTanggalPlp(): self
    {
        return new self('PLP date cannot be empty.');
    }

    public static function invalidTanggalPlpFormat(mixed $date): self
    {
        $value = is_scalar($date) ? (string) $date : get_debug_type($date);

        return new self(sprintf('PLP date "%s" is invalid or could not be parsed.', $value));
    }
}

