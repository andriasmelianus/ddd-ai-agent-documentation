<?php

declare(strict_types=1);

namespace Src\Partner\Domain\ValueObjects;

use Stringable;

/**
 * Class TransactionTypeCode
 *
 * Immutable Value Object for Indonesian Tax Invoice Transaction Code (Kode Jenis Transaksi 01-09).
 */
readonly class TransactionTypeCode implements Stringable
{
    public const string DEFAULT = '01';

    public string $value;

    public function __construct(?string $value = null)
    {
        $val = ($value !== null && trim($value) !== '') ? trim($value) : self::DEFAULT;
        $this->value = str_pad($val, 2, '0', STR_PAD_LEFT);
    }

    public static function fromString(?string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
