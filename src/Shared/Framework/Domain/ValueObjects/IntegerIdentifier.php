<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\ValueObjects;

use JsonSerializable;
use Src\Shared\Framework\Domain\Exceptions\InvalidIdentifierException;

/**
 * Class IntegerIdentifier
 *
 * Value Object dasar untuk Identifier bertipe **integer** (mis. primary
 * key auto-increment di database relasional).
 *
 * Karakteristik:
 *  - Nilai `0` dianggap tidak valid — melempar
 *    {@see InvalidIdentifierException}.
 *  - Mengimplementasikan {@see IdentifierInterface} dan
 *    {@see \JsonSerializable} sehingga aman disematkan di payload JSON.
 *  - Constructor `final` — subclass wajib memakai constructor ini,
 *    kustomisasi dilakukan lewat factory `create()`/`createOrNull()`.
 *
 * Digunakan sebagai base class untuk mis.
 * {@see AutoIncrementIdentifier}.
 */
class IntegerIdentifier implements IdentifierInterface, JsonSerializable
{
    /**
     * Nilai integer internal yang dibungkus Identifier ini.
     * Default `-1` menandakan nilai belum di-set (mis. untuk kasus
     * auto-increment sebelum insert ke DB).
     */
    protected int $value = -1;

    /**
     * Factory statis untuk membuat instance dari nilai mentah.
     *
     * Menerima `int` atau `string` numerik. Nilai `0` dianggap invalid
     * dan menyebabkan {@see InvalidIdentifierException}.
     *
     * @param  int|string  $value  Nilai mentah Identifier.
     * @return static Instance Identifier ter-validasi.
     *
     * @throws InvalidIdentifierException Jika nilai `= 0` (setelah cast int).
     */
    public static function create(int|string $value): self
    {
        $intValue = (int) $value;
        if ($intValue === 0) {
            throw new InvalidIdentifierException(static::class, $intValue);
        }

        return new static($intValue);
    }

    /**
     * Factory statis yang **null-safe**.
     *
     * Mengembalikan `null` bila input adalah `null` atau `0`. Untuk nilai
     * lain yang setelah cast menjadi `0` akan tetap dilempar
     * {@see InvalidIdentifierException}.
     *
     * @param  int|string|null  $value  Nilai mentah Identifier atau `null`.
     * @return static|null Instance Identifier, atau `null` bila input
     *                     `null`/`0`.
     *
     * @throws InvalidIdentifierException Jika string yang non-null menghasilkan `0` saat di-cast.
     */
    public static function createOrNull(int|string|null $value): ?self
    {
        if ($value === 0) {
            return null;
        }
        if ($value === null) {
            return null;
        }
        $intValue = (int) $value;
        if ($intValue === 0) {
            throw new InvalidIdentifierException(static::class, $intValue);
        }

        return new static($intValue);
    }

    /**
     * Membuat instance IntegerIdentifier.
     *
     * @param  int  $value  Nilai integer yang telah tervalidasi. Sengaja
     *                      `final` agar subclass tidak dapat mengubah invariants.
     */
    final public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * Mengembalikan nilai integer yang dibungkus.
     *
     * @return int Nilai Identifier.
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * Alias {@see self::getValue()} — disediakan untuk kompatibilitas
     * dengan gaya penamaan Copilot / ekosistem yang memakai `value()`.
     *
     * @return int Nilai Identifier.
     */
    public function value(): int
    {
        return $this->getValue();
    }

    /**
     * Membandingkan kesetaraan dua Identifier berdasarkan nilainya.
     *
     * @param  IdentifierInterface  $other  Identifier lain yang dibandingkan.
     * @return bool `true` jika nilainya sama, `false` jika berbeda.
     */
    public function equals(IdentifierInterface $other): bool
    {
        return $this->getValue() === $other->getValue();
    }

    /**
     * Representasi string dari Identifier.
     *
     * @return string String hasil casting nilai integer.
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * Serialisasi ke JSON — Identifier di-encode sebagai bilangan bulat.
     *
     * @return int Nilai integer Identifier.
     */
    public function jsonSerialize(): int
    {
        return $this->value;
    }
}
