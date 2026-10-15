<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Helpers;

use BackedEnum;
use Src\Shared\Framework\Domain\Entities\BaseEntity;
use Src\Shared\Framework\Domain\ValueObjects\IdentifierInterface;
use Src\Shared\Framework\Domain\ValueObjects\IntegerIdentifier;
use Src\Shared\Framework\Domain\ValueObjects\ValueObject;
use Src\Shared\Framework\Infrastructure\Logging\LoggerHelper;
use stdClass;
use Throwable;

use function array_key_exists;
use function count;
use function is_array;
use function is_float;
use function is_object;

/**
 * Helper utilitas untuk manipulasi array.
 *
 * Kelas ini menyediakan sekumpulan metode statis yang mempermudah operasi
 * umum terhadap array pada lapisan domain/aplikasi, seperti:
 *  - Normalisasi kunci array (mis. konversi ke huruf kecil).
 *  - Konversi antara array identifier (Value Object) dan array int/string.
 *  - Konversi array string menjadi array BackedEnum.
 *  - Perbandingan dua array secara rekursif dengan toleransi float.
 *  - Ekstraksi id dari kumpulan entitas domain.
 *
 * Semua metode bersifat pure/statis dan tidak menyimpan state internal.
 */
final class ArrayHelper
{
    /**
     * Mengubah seluruh kunci array (termasuk sub-array) menjadi huruf kecil.
     *
     * Fungsi ini bekerja secara rekursif: jika salah satu nilai adalah array,
     * kunci-kuncinya juga akan diubah menjadi huruf kecil. Kunci non-string
     * (mis. integer) dibiarkan apa adanya.
     *
     * @param  array<mixed>  $array  Array sumber yang akan dinormalisasi.
     * @return array<mixed> Array baru dengan seluruh kunci string dalam huruf kecil.
     */
    public static function arrayKeysToLowercase(array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $key = is_string($key) ? strtolower($key) : $key;
            if (is_array($value)) {
                $value = self::arrayKeysToLowercase($value);
            }
            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * Mengonversi array {@see IntegerIdentifier} menjadi array integer.
     *
     * Mengembalikan `null` jika input `null`, sehingga aman digunakan pada
     * konteks nullable (mis. parameter opsional query).
     *
     * @param  IntegerIdentifier[]|null  $valueObjects  Kumpulan value object identifier.
     * @return int[]|null Array integer, atau null bila input null.
     */
    public static function identityArrayToInt(?array $valueObjects): ?array
    {
        if ($valueObjects === null) {
            return null;
        }

        return array_map(static function (IntegerIdentifier $item) {
            return $item->getValue();
        }, $valueObjects);
    }

    /**
     * Menggabungkan array-array pada level pertama menjadi satu array datar.
     *
     * Contoh:
     *  Input:  [['a','b'], ['c']]
     *  Output: ['a','b','c']
     *
     * Jika `$keepKey = true`, kunci asosiatif pada sub-array akan dipertahankan
     * (nilai dengan kunci sama akan saling menimpa).
     *
     * @param  array<int|string,array<mixed>>  $items  Array dari array yang akan digabungkan.
     * @param  bool  $keepKey  Pertahankan kunci asosiatif sub-array.
     * @return array<mixed> Array hasil gabungan.
     */
    public static function mergeFirstLevel(array $items, bool $keepKey = false): array
    {
        $result = [];
        foreach ($items as $subArray) {
            foreach ($subArray as $key => $item) {
                if ($keepKey) {
                    $result[$key] = $item;
                } else {
                    $result[] = $item;
                }
            }
        }

        return $result;
    }

    /**
     * Mengubah array integer menjadi array instance identifier bertipe `T`.
     *
     * Berguna untuk merehidrasi id mentah (dari DB / request) menjadi
     * Value Object identifier yang typed.
     *
     * @template T
     *
     * @param  int[]  $array  Array id bertipe integer.
     * @param  class-string<T>  $class  Nama class identifier yang akan diinstansiasi.
     * @return T[] Array instance dari class `T`.
     */
    public static function intToIdentityArray(array $array, string $class): array
    {
        return array_map(static function (int $id) use ($class) {
            return new $class($id);
        }, $array);
    }

    /**
     * Mengubah tipe (class) dari sekumpulan identifier tanpa mengubah nilainya.
     *
     * Berguna ketika sebuah id lintas-bounded-context perlu dipetakan ke
     * class identifier yang berbeda namun berbagi nilai yang sama.
     *
     * @template T
     *
     * @param  array<IdentifierInterface>  $array  Array identifier asal.
     * @param  class-string<T>  $class  Class identifier tujuan.
     * @return T[] Array instance dari class `T`.
     */
    public static function mutateIdentityArray(array $array, string $class): array
    {
        return array_map(static function (IdentifierInterface $id) use ($class) {
            return new $class($id->getValue());
        }, $array);
    }

    /**
     * Mengubah array string menjadi array instance identifier bertipe `T`.
     *
     * @template T of IdentifierInterface
     *
     * @param  string[]  $array  Array id bertipe string.
     * @param  class-string<T>  $class  Class identifier yang akan diinstansiasi.
     * @return array<T> Array instance identifier.
     */
    public static function stringToIdentityArray(array $array, $class): array
    {
        /** @var array<T> $res */
        $res = array_map(static function (string $id) use ($class) {
            return new $class($id);
        }, $array);

        return $res;
    }

    /**
     * Mengonversi array {@see IdentifierInterface} menjadi array string.
     *
     * Mengembalikan `null` jika input `null`.
     *
     * @param  IdentifierInterface[]|null  $valueObjects  Array value object identifier.
     * @return string[]|null Array string dari nilai identifier.
     */
    public static function identityArrayToString(?array $valueObjects): ?array
    {
        if ($valueObjects === null) {
            return null;
        }

        return array_map(static function ($item) {
            return (string) $item->getValue();
        }, $valueObjects);
    }

    /**
     * Mengonversi array {@see BackedEnum} menjadi array string dari nilainya.
     *
     * @param  BackedEnum[]|null  $valueObjects  Array backed enum.
     * @return string[]|null Array string dari properti `->value` masing-masing enum.
     */
    public static function backedEnumArrayToString(?array $valueObjects): ?array
    {
        if ($valueObjects === null) {
            return null;
        }

        return array_map(static function (BackedEnum $item) {
            return (string) $item->value;
        }, $valueObjects);
    }

    /**
     * Mengonversi Value Object menjadi bentuk primitif (scalar / array).
     *
     * Aturan konversi:
     *  - `IdentifierInterface` di-unwrap menjadi nilai primitifnya.
     *  - `ValueObject` bersarang dikonversi secara rekursif.
     *  - Jika hasil hanya memiliki satu key, dikembalikan nilai tunggalnya
     *    (agar VO "single-value" tampil sebagai skalar, bukan array).
     *
     * @param  ValueObject|null  $valueObject  Value object yang akan dikonversi.
     * @return null|string|int|array<string,mixed>
     *
     * @phpstan-ignore-next-line
     */
    public static function valueObjectToArray(?ValueObject $valueObject): ?array
    {
        if ($valueObject === null) {
            return null;
        }
        $result = [];
        $attributes = get_object_vars($valueObject);
        foreach ($attributes as $key => $attribute) {
            if ($attribute instanceof IdentifierInterface) {
                $attribute = $attribute->getValue();
            }
            if ($attribute instanceof ValueObject) {
                $attribute = self::valueObjectToArray($attribute);
            }
            $result[$key] = $attribute;
        }
        if (count($result) === 1) {
            /** @phpstan-ignore-next-line */
            return current($result);
        }

        return $result;
    }

    /**
     * Mengambil nilai float dari sebuah attribute pada array, atau `null` bila tidak ada.
     *
     * @param  array<string,mixed>  $array  Array sumber.
     * @param  string  $attribute  Nama attribute yang ingin diambil.
     * @return float|null Nilai float, atau null bila attribute null/tidak ada.
     */
    public static function getFloatOrNull(array $array, string $attribute): ?float
    {
        $data = $array[$attribute] ?? null;
        if ($data === null) {
            return null;
        }

        return MixedHelper::getFloat($data);
    }

    /**
     * Mengambil nilai int dari sebuah attribute pada array, atau `null` bila tidak ada.
     *
     * @param  array<string,mixed>  $array  Array sumber.
     * @param  string  $attribute  Nama attribute yang ingin diambil.
     * @return int|null Nilai int, atau null bila attribute null/tidak ada.
     */
    public static function getIntOrNull(array $array, string $attribute): ?int
    {
        $data = $array[$attribute] ?? null;
        if ($data === null) {
            return null;
        }

        return MixedHelper::getInt($data);
    }

    /**
     * Mengambil nilai string dari sebuah attribute pada array, atau `null` bila tidak ada.
     *
     * @param  array<string,mixed>  $array  Array sumber.
     * @param  string  $attribute  Nama attribute yang ingin diambil.
     * @return string|null Nilai string, atau null bila attribute null/tidak ada.
     */
    public static function getStringOrNull(array $array, string $attribute): ?string
    {
        $data = $array[$attribute] ?? null;
        if ($data === null) {
            return null;
        }

        return MixedHelper::getString($data);
    }

    /**
     * Mengambil nilai bool dari sebuah attribute pada array, atau `null` bila tidak ada.
     *
     * @param  array<string,mixed>  $array  Array sumber.
     * @param  string  $attribute  Nama attribute yang ingin diambil.
     * @return bool|null Nilai bool, atau null bila attribute null/tidak ada.
     */
    public static function getBoolOrNull(array $array, string $attribute): ?bool
    {
        $data = $array[$attribute] ?? null;
        if ($data === null) {
            return null;
        }

        return MixedHelper::getBoolean($data);
    }

    /**
     * Membandingkan dua array secara rekursif dengan aturan khusus per tipe.
     *
     * Aturan pembandingan:
     *  - Kunci pada `$array1` harus ada pada `$array2`.
     *  - Nilai berupa array dibandingkan secara rekursif melalui metode ini.
     *  - Nilai berupa object dibandingkan berdasarkan hasil JSON encoding
     *    (dengan {@see MixedHelper::safeJson()}).
     *  - Nilai float dibandingkan dengan toleransi `0.00001` untuk menghindari
     *    masalah presisi floating point (mis. hasil `json_decode`).
     *  - Tipe lain dibandingkan secara strict (`!==`).
     *
     * @param  array<mixed>  $array1  Array pertama.
     * @param  array<mixed>  $array2  Array kedua.
     * @return bool True bila kedua array dianggap ekuivalen.
     */
    public static function arrayEqual(array $array1, array $array2): bool
    {
        if (count($array1) !== count($array2)) {
            return false;
        }
        foreach ($array1 as $key => $value) {
            if (! array_key_exists($key, $array2)) {
                return false;
            }
            // Jika kedua nilai adalah array, lakukan perbandingan rekursif
            if (is_array($value) && is_array($array2[$key])) {
                if (! self::arrayEqual($value, $array2[$key])) {
                    return false;
                }
            } // Jika kedua nilai adalah objek, bandingkan sebagai JSON
            elseif (is_object($value) && is_object($array2[$key])) {
                if (MixedHelper::safeJson($value) !== MixedHelper::safeJson($array2[$key])) {
                    return false;
                }
            } // Jika kedua nilai adalah float, bandingkan dengan toleransi
            elseif (is_float($value) && is_float($array2[$key])) {
                if (abs($value - $array2[$key]) > 0.00001) {
                    return false;
                }
            } // Untuk semua kasus lainnya, gunakan perbandingan strict
            elseif ($value !== $array2[$key]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Mengubah array string menjadi array {@see BackedEnum}.
     *
     * Nilai yang tidak valid (menyebabkan `Throwable` saat `from()`) akan
     * dicatat ke log via {@see LoggerHelper::logException()} dan diskip.
     *
     * @template T of BackedEnum
     *
     * @param  string[]  $array  Array string yang akan dikonversi.
     * @param  class-string<T>  $class  Nama class {@see BackedEnum}.
     * @return T[] Array instance BackedEnum yang berhasil dikonversi.
     */
    public static function stringToBackedEnum(array $array, string $class): array
    {
        /** @var T[] $res */
        $res = [];
        foreach ($array as $value) {
            try {
                $res[] = $class::from($value);
            } catch (Throwable $e) {
                LoggerHelper::logException($e);

                continue;
            }
        }

        return $res;
    }

    /**
     * Versi lebih ketat dari {@see self::stringToBackedEnum()} — tanpa error handling.
     *
     * Berbeda dengan `stringToBackedEnum`, metode ini akan melempar exception
     * bila salah satu nilai tidak dapat dipetakan ke enum.
     *
     * @template T
     *
     * @param  string[]  $array  Array string.
     * @param  class-string<T>  $class  Nama class {@see BackedEnum}.
     * @return T[] Array instance enum.
     */
    public static function tagToBackedEnum(array $array, string $class): array
    {
        /** @var T[] $res */
        $res = array_map(static function (string $value) use ($class) {
            /** @var BackedEnum $baseEnum */
            /** @phpstan-ignore-next-line */
            $baseEnum = $class;

            return $baseEnum::from($value);
        }, $array);

        return $res;
    }

    /**
     * Mengembalikan array unik dengan dukungan perbandingan object.
     *
     * Berbeda dengan `array_unique` bawaan PHP, metode ini:
     *  - Mengonversi object menjadi array sebelum perbandingan,
     *    sehingga dua object dengan property identik dianggap sama.
     *  - Opsional mempertahankan kunci asosiatif via `$keepKeyAssoc`.
     *
     * @template T
     *
     * @param  array<T>  $array  Array yang akan dinormalisasi.
     * @param  bool  $keepKeyAssoc  Pertahankan kunci asosiatif (default: false).
     * @return array<T> Array tanpa duplikat.
     */
    public static function arrayUnique(array $array, bool $keepKeyAssoc = false): array
    {
        $duplicateKeys = [];
        $tmp = [];

        foreach ($array as $key => $val) {
            if (is_object($val)) {
                $val = (array) $val;
            }

            if (! in_array($val, $tmp)) {
                $tmp[] = $val;
            } else {
                $duplicateKeys[] = $key;
            }
        }

        foreach ($duplicateKeys as $key) {
            unset($array[$key]);
        }

        return $keepKeyAssoc ? $array : array_values($array);
    }

    /**
     * Mengambil kumpulan id dari array entitas domain.
     *
     * @param  BaseEntity[]  $entities  Array entitas domain.
     * @return IdentifierInterface[] Array identifier hasil `pluck('id')`.
     */
    public static function extractEntitiesIds(array $entities): array
    {
        /** @var IdentifierInterface[] $identities */
        $identities = collect($entities)->pluck('id')->all();

        return $identities;
    }

    /**
     * Mengambil kumpulan id dari array entitas domain dalam bentuk integer.
     *
     * @param  array<BaseEntity>  $entities  Array entitas domain.
     * @return int[] Array id bertipe integer.
     */
    public static function extractEntitiesIdsAsInt(array $entities): array
    {
        /** @var IdentifierInterface[] $identities */
        $identities = collect($entities)->pluck('id')->all();

        return array_map(static fn ($id) => (int) $id->getValue(), $identities);
    }

    /**
     * Mengambil kumpulan id dari array entitas domain dalam bentuk string.
     *
     * @param  array<BaseEntity>  $entities  Array entitas domain.
     * @return string[] Array id bertipe string.
     */
    public static function extractEntitiesIdsAsString(array $entities): array
    {
        /** @var IdentifierInterface[] $identities */
        $identities = collect($entities)->pluck('id')->all();

        return array_map(static fn ($id) => (string) $id->getValue(), $identities);
    }

    /**
     * Mengonversi array integer menjadi array string.
     *
     * @param  array<int>  $items  Array integer.
     * @return array<string> Array string dari nilai yang sama.
     */
    public static function intToString(array $items): array
    {
        return array_map(static function (int $id) {
            return (string) $id;
        }, $items);
    }

    /**
     * Mengembalikan data apa adanya, atau {@see stdClass} kosong bila `empty`.
     *
     * Berguna untuk memastikan field JSON pada respons API tetap berbentuk
     * object `{}` (bukan array `[]`) ketika nilainya kosong.
     *
     * @template T
     *
     * @param  T  $data  Data yang akan diperiksa.
     * @return T|stdClass
     */
    public static function safeJson($data): mixed
    {
        if (empty($data)) {
            return new stdClass;
        }

        return $data;
    }
}
