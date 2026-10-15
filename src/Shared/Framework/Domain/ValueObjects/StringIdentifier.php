<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\ValueObjects;

use JsonSerializable;
use RuntimeException;

/**
 * Class StringIdentifier
 *
 * Value Object dasar untuk Identifier bertipe **string** (mis. kode
 * bisnis, slug, alfanumerik).
 *
 * Karakteristik:
 *  - String kosong `''` dianggap invalid — melempar {@see \RuntimeException}.
 *  - Mengimplementasikan {@see IdentifierInterface} dan
 *    {@see \JsonSerializable}.
 *  - Constructor `final` agar invariants terjaga; kustomisasi dilakukan
 *    lewat factory `create()`/`createOrNull()`.
 *
 * Digunakan sebagai base class untuk Identifier alfanumerik spesifik
 * seperti {@see CountryCode}.
 */
class StringIdentifier implements IdentifierInterface, JsonSerializable
{
    /**
     * Nilai string internal yang dibungkus.
     */
    private string $value;

    /**
     * Factory statis untuk membuat instance dari nilai mentah.
     *
     * @param  int|string  $value  Nilai identifier (akan dicast ke string).
     * @return static Instance Identifier ter-validasi.
     *
     * @throws RuntimeException Jika `$value` adalah string kosong `''`.
     */
    public static function create(int|string $value): self
    {
        if ($value === '') {
            throw new RuntimeException('Error Empty String Id');
        }

        return new static((string) $value);
    }

    /**
     * Serialisasi ke JSON — Identifier di-encode sebagai string.
     *
     * @return string Nilai string Identifier.
     */
    public function jsonSerialize(): string
    {
        return $this->value;
    }

    /**
     * Factory statis **null-safe**.
     *
     * Mengembalikan `null` untuk input `null` atau string kosong.
     *
     * @param  int|string|null  $value  Nilai mentah atau `null`.
     * @return static|null Instance Identifier, atau `null` bila input
     *                     `null` atau `''`.
     */
    public static function createOrNull(int|string|null $value): ?self
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return new static((string) $value);
    }

    /**
     * Membuat instance StringIdentifier.
     *
     * @param  string  $value  Nilai string yang telah tervalidasi. `final`
     *                         agar subclass tidak mengubah invariants.
     */
    final public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Mengembalikan nilai string yang dibungkus.
     *
     * @return string Nilai Identifier.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Alias {@see self::getValue()} — disediakan untuk kompatibilitas
     * gaya penamaan `value()`.
     *
     * @return string Nilai Identifier.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Menetapkan nilai baru pada Identifier ini.
     *
     * @param  string  $value  Nilai baru.
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
