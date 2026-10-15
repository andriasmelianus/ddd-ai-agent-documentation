<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\ValueObjects;

use Illuminate\Support\Str;
use InvalidArgumentException;
use JsonSerializable;

/**
 * Class Ulid
 *
 * Value Object untuk **ULID** (Universally Unique Lexicographically
 * Sortable Identifier) — sebuah identifier 128-bit yang, berbeda dengan
 * UUIDv4, bersifat **monotonic/lexicographically sortable** berdasarkan
 * waktu pembuatan.
 *
 * Validasi menggunakan `Symfony\Component\Uid\Ulid::isValid()`; ID yang
 * tidak valid akan menyebabkan {@see \InvalidArgumentException} di
 * constructor.
 *
 * Mengimplementasikan {@see IdentifierInterface} dan
 * {@see \JsonSerializable}.
 */
class Ulid implements IdentifierInterface, JsonSerializable
{
    /**
     * Nilai ULID string yang telah tervalidasi.
     */
    protected string $value;

    /**
     * Membuat instance Ulid dari string yang tervalidasi.
     *
     * @param  string  $value  ULID string.
     *
     * @throws InvalidArgumentException Jika `$value` bukan ULID yang valid.
     */
    final public function __construct(string $value)
    {
        $this->ensureIsValidUuid($value);
        $this->value = $value;
    }

    /**
     * Serialisasi ke JSON — ULID di-encode sebagai string.
     *
     * @return string Nilai ULID.
     */
    public function jsonSerialize(): string
    {
        return $this->value;
    }

    /**
     * Membuat instance dengan ULID baru yang di-generate acak
     * (menggunakan {@see \Illuminate\Support\Str::ulid()}).
     *
     * @return static ULID baru.
     */
    public static function random(): self
    {
        return static::createInstance((string) Str::ulid());
    }

    /**
     * Mengembalikan nilai ULID string.
     *
     * @return string Nilai ULID.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Alias {@see self::getValue()} — disediakan untuk kompatibilitas
     * penamaan `value()`.
     *
     * @return string Nilai ULID.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Membandingkan kesetaraan dua ULID berdasarkan nilai stringnya.
     *
     * @param  self  $other  ULID lain untuk dibandingkan.
     * @return bool `true` jika nilainya sama.
     */
    public function equals(self $other): bool
    {
        return $this->getValue() === $other->getValue();
    }

    /**
     * Representasi string dari ULID.
     *
     * @return string Nilai ULID.
     */
    public function __toString(): string
    {
        return $this->getValue();
    }

    /**
     * Memastikan `$id` adalah ULID yang valid; melempar exception jika tidak.
     *
     * @param  string  $id  String yang akan divalidasi.
     *
     * @throws InvalidArgumentException Jika `$id` tidak valid sebagai ULID.
     */
    private function ensureIsValidUuid(string $id): void
    {
        if (\Symfony\Component\Uid\Ulid::isValid($id) === false) {
            throw new InvalidArgumentException(sprintf('<%s> does not allow the value <%s>.', static::class, $id));
        }
    }

    /**
     * Factory statis dari nilai mentah (string atau int).
     *
     * @param  string|int  $value  Nilai mentah ULID (akan di-cast ke string).
     * @return static Instance ULID.
     */
    public static function create(string|int $value): self
    {
        return static::createInstance((string) $value);
    }

    /**
     * Factory statis **null-safe**.
     *
     * @param  string|int|null  $value  Nilai mentah atau `null`.
     * @return static|null Instance ULID, atau `null` bila input `null`.
     */
    public static function createOrNull(null|string|int $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return static::createInstance((string) $value);
    }

    /**
     * Factory alternatif dari string.
     *
     * @param  string  $value  Nilai ULID string.
     * @return static Instance ULID.
     */
    public static function fromString(string $value): self
    {
        return static::createInstance($value);
    }

    /**
     * Factory alternatif dari string dengan dukungan `null`.
     *
     * @param  string|null  $value  Nilai ULID string atau `null`.
     * @return static|null Instance ULID, atau `null` bila input `null`.
     */
    public static function fromStringOrNull(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return static::createInstance($value);
    }

    /**
     * Helper internal untuk membuat instance baru.
     *
     * @param  string  $value  Nilai ULID string.
     * @return static Instance baru.
     */
    protected static function createInstance(string $value): static
    {
        return new static($value);
    }
}
