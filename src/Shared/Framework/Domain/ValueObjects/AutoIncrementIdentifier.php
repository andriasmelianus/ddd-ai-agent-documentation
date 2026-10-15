<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\ValueObjects;

use Illuminate\Support\Str;

/**
 * Class AutoIncrementIdentifier
 *
 * Ekstensi {@see IntegerIdentifier} untuk merepresentasikan primary key
 * **auto-increment** yang **belum di-generate** oleh database.
 *
 * Pola pemakaian:
 *  1. Sebelum insert ke database, entity baru diberi identifier via
 *     {@see self::autoincrement()} — instance dibuat dengan nilai
 *     sementara `-1` dan penanda unik ({@see self::$random}).
 *  2. Setelah baris ter-insert dan DB mengembalikan ID hasil auto-increment,
 *     panggil {@see self::setAutoIncrement()} untuk menetapkan nilai
 *     final. Nilai final juga disimpan di map statis
 *     {@see self::$lastAutoincrement} yang diindeks oleh key `$random`.
 *  3. Semua panggilan {@see self::getValue()}/{@see self::value()}
 *     setelah itu akan mengembalikan nilai final.
 *
 * Map statis dipakai agar seluruh referensi instance yang sama
 * (misalnya beberapa VO yang berbagi key `$random`) tetap sinkron
 * ketika ID akhirnya ditetapkan.
 */
class AutoIncrementIdentifier extends IntegerIdentifier
{
    /**
     * Map statis {@code random => id} yang berisi ID final untuk
     * setiap identifier auto-increment.
     *
     * Key adalah UUID acak yang dihasilkan {@see self::autoincrement()},
     * sedangkan value adalah nilai ID final (nilai `-1` menandakan
     * belum ditetapkan).
     *
     * @var array<string|int,int>
     */
    public static array $lastAutoincrement = [];

    /**
     * Penanda unik (UUID) untuk melacak instance ini pada
     * {@see self::$lastAutoincrement}.
     *
     * Perlu bertipe `string` public (bukan readonly) agar kompatibel
     * dengan mekanisme serialization Laravel/PHP.
     */
    public string $random = 'INITIAL_VALUE'; // needed for serialization

    /**
     * Membuat identifier baru yang bersifat **auto-increment** dan
     * belum memiliki nilai final.
     *
     * Instance yang dibuat memiliki nilai internal `-1` dan penanda
     * `$random` unik. Setelah insert DB, nilai final ditetapkan lewat
     * {@see self::setAutoIncrement()}.
     *
     * @return static Instance identifier auto-increment baru.
     */
    public static function autoincrement(): self
    {
        $random = (string) Str::uuid();
        static::$lastAutoincrement[$random] = -1;
        $instance = new static(-1);
        $instance->random = $random;

        return $instance;
    }

    /**
     * Mengembalikan nilai identifier saat ini.
     *
     * Jika nilai internal masih `-1` (belum di-set), akan mencoba
     * membaca nilai final dari map {@see self::$lastAutoincrement}
     * berdasarkan `$random`. Jika belum ada, tetap mengembalikan `-1`.
     *
     * @return int Nilai identifier (final bila sudah di-set, atau `-1`
     *             bila masih menunggu insert).
     */
    public function getValue(): int
    {
        if (parent::getValue() === -1) {
            return static::$lastAutoincrement[$this->random] ?? parent::getValue();
        }

        return parent::getValue();
    }

    /**
     * Alias {@see self::getValue()} — disediakan untuk kompatibilitas
     * penamaan `value()`.
     *
     * @return int Nilai identifier saat ini.
     */
    public function value(): int
    {
        if (parent::getValue() === -1) {
            return static::$lastAutoincrement[$this->random] ?? parent::getValue();
        }

        return parent::getValue();
    }

    /**
     * Menetapkan nilai ID final setelah DB mengembalikan hasil
     * auto-increment. Nilai disimpan baik di properti instance maupun
     * di map statis agar seluruh referensi tetap sinkron.
     *
     * @param  int  $value  Nilai ID final dari database.
     */
    public function setAutoIncrement(int $value): void
    {
        static::$lastAutoincrement[$this->random] = $value;
        $this->value = $value;
    }

    /**
     * Representasi string identifier.
     *
     * @return string Nilai ID (final atau `-1`) sebagai string.
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }

    /**
     * Factory alternatif untuk membuat instance dari integer eksplisit.
     *
     * Berguna ketika identifier sudah diketahui sebelumnya (mis. berasal
     * dari DB) dan tidak membutuhkan mekanisme auto-increment.
     *
     * @param  int  $value  Nilai integer eksplisit.
     * @return static Instance identifier baru.
     */
    public static function fromInt(int $value): self
    {
        return self::create($value);
    }
}
