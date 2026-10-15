<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;
use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * Class Uuid
 *
 * Value Object untuk **UUID** (Universally Unique Identifier) berbasis
 * pustaka {@see \Ramsey\Uuid\Uuid}. Menerima berbagai versi UUID selama
 * lolos validasi `RamseyUuid::isValid()`.
 *
 * Karakteristik:
 *  - Constructor `final` — memastikan invariants (nilai valid & immutable).
 *  - `random()` menghasilkan UUIDv4 baru.
 *  - Mengimplementasikan {@see IdentifierInterface} & {@see \JsonSerializable}.
 */
class Uuid implements IdentifierInterface, JsonSerializable
{
    /**
     * Nilai UUID string yang telah tervalidasi.
     */
    protected string $value;

    /**
     * Membuat instance Uuid dari string tervalidasi.
     *
     * @param  string  $value  UUID string.
     *
     * @throws InvalidArgumentException Jika `$value` bukan UUID valid.
     */
    final public function __construct(string $value)
    {
        $this->ensureIsValidUuid($value);
        $this->value = $value;
    }

    /**
     * Serialisasi ke JSON — UUID di-encode sebagai string.
     *
     * @return string Nilai UUID.
     */
    public function jsonSerialize(): string
    {
        return $this->value;
    }

    /**
     * Membuat instance dengan UUIDv4 baru yang di-generate acak.
     *
     * @return static UUID baru.
     */
    public static function random(): static
    {
        return static::createInstance(RamseyUuid::uuid4()->toString());
    }

    /**
     * Helper internal untuk membuat instance baru.
     *
     * @param  string  $value  Nilai UUID string.
     * @return static Instance baru.
     */
    protected static function createInstance(string $value): static
    {
        return new static($value);
    }

    /**
     * Mengembalikan nilai UUID string.
     *
     * @return string Nilai UUID.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Alias {@see self::getValue()}.
     *
     * @return string Nilai UUID.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Membandingkan kesetaraan dua UUID berdasarkan nilai stringnya.
     *
     * @param  self  $other  UUID lain untuk dibandingkan.
     * @return bool `true` jika nilainya sama.
     */
    public function equals(self $other): bool
    {
        return $this->getValue() === $other->getValue();
    }

    /**
     * Representasi string UUID.
     *
     * @return string Nilai UUID.
     */
    public function __toString(): string
    {
        return $this->getValue();
    }

    /**
     * Memastikan `$id` adalah UUID valid; melempar exception jika tidak.
     *
     * @param  string  $id  String yang akan divalidasi.
     *
     * @throws InvalidArgumentException Jika `$id` tidak valid sebagai UUID.
     */
    private function ensureIsValidUuid(string $id): void
    {
        if (RamseyUuid::isValid($id) === false) {
            throw new InvalidArgumentException(sprintf('<%s> does not allow the value <%s>.', static::class, $id));
        }
    }

    /**
     * Factory statis dari nilai mentah.
     *
     * @param  string|int  $value  Nilai mentah UUID (akan di-cast ke string).
     * @return static Instance UUID.
     */
    public static function create(string|int $value): static
    {
        return static::createInstance((string) $value);
    }

    /**
     * Factory statis **null-safe**.
     *
     * @param  string|int|null  $value  Nilai mentah atau `null`.
     * @return static|null Instance UUID, atau `null` bila input `null`.
     */
    public static function createOrNull(null|string|int $value): ?static
    {
        if ($value === null) {
            return null;
        }

        return static::createInstance((string) $value);
    }

    /**
     * Factory alternatif dari string.
     *
     * @param  string  $value  Nilai UUID string.
     * @return static Instance UUID.
     */
    public static function fromString(string $value): static
    {
        return static::createInstance($value);
    }
}
