<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\Entities;

use BackedEnum;
use Carbon\Carbon as BaseCarbon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use JsonException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use RuntimeException;
use Src\Shared\Framework\Domain\ValueObjects\IdentifierInterface;
use Src\Shared\Framework\Domain\ValueObjects\ValueObjectInterface;

use function array_key_exists;
use function is_int;
use function is_string;

/**
 * Utility hidrasi entitas yang mengonversi array atribut mentah
 * (biasanya dari Eloquent Model) menjadi nilai bertipe kaya sesuai
 * deklarasi properti kelas Domain (Value Object, Identifier, Enum,
 * Carbon, array JSON, dsb).
 *
 * Kelas ini dipakai internal oleh {@see BaseEntity::autoHydrate()} dan
 * di-cache hasil reflection-nya agar performanya efisien meskipun
 * dipanggil berulang kali pada koleksi besar.
 */
final class BaseEntityHydrater
{
    /**
     * Cache internal untuk hasil reflection (tipe properti & ReflectionClass)
     * agar tidak diulang antar-panggilan.
     *
     * @var array<string,mixed>
     */
    private static array $reflectionCache = [];

    /**
     * Menghitung nilai properti target entitas dari array atribut mentah,
     * dengan mengonversi setiap nilai sesuai tipe properti (via reflection).
     *
     * Properti yang tidak memiliki deklarasi tipe akan diabaikan (skip).
     * Bila reflection gagal untuk atribut tertentu, cache-nya akan diisi
     * null agar tidak diulang di panggilan berikutnya.
     *
     * @param  array<string,mixed>  $array  Peta atribut mentah [nama => nilai].
     * @param  ReflectionClass  $class  Reflection kelas entitas target.
     * @return array<string,mixed> Peta [namaProperti => nilaiTypedReady].
     */
    public static function calculateAttributesFromArray(
        array $array,
        ReflectionClass $class
    ): array {

        $params = [];
        foreach ($array as $attribute => $value) {
            $key = 'type_'.$class->getFileName().$attribute;
            try {

                if (array_key_exists($key, self::$reflectionCache)) {
                    /** @var ReflectionNamedType|null $type */
                    $type = self::$reflectionCache[$key];
                } else {
                    /** @var ReflectionNamedType|null $type */
                    $type = $class->getProperty($attribute)->getType();
                    self::$reflectionCache[$key] = $type;
                }

                if ($type === null) {
                    continue;
                }

                if ($value === null) {
                    $params[$attribute] = null;
                } else {
                    $params[$attribute] = self::calculateReflectionValue($type, $value);
                }
            } catch (ReflectionException) {
                self::$reflectionCache[$key] = null;
            }
        }

        return $params;
    }

    /**
     * Mengonversi sebuah nilai mentah menjadi tipe yang sesuai dengan
     * {@see ReflectionNamedType} target.
     *
     * Aturan konversi utama:
     *  - `array` (string JSON)             → array hasil `json_decode`.
     *  - Enum backed (string|int)          → instance enum via `::from()`.
     *  - Implementor {@see IdentifierInterface} → dibuat via `::create()` statis.
     *  - Kelas `Carbon\Carbon` langsung    → dilarang (throw RuntimeException).
     *  - `Illuminate\Support\Carbon`       → dibuat dari timestamp atau string tanggal.
     *  - Implementor {@see ValueObjectInterface} → di-hydrate lewat
     *    {@see updateSingleValueObject()} (kecuali Identifier + value string kosong → null).
     *
     * @param  ReflectionNamedType  $type  Tipe target properti.
     * @param  mixed  $value  Nilai mentah.
     * @return IdentifierInterface|array|mixed Nilai yang telah dikonversi.
     *
     * @throws ReflectionException Bila reflection kelas target gagal.
     * @throws RuntimeException Bila format JSON invalid atau Carbon dipakai langsung.
     */
    private static function calculateReflectionValue(ReflectionNamedType $type, mixed $value): mixed
    {
        $newValue = $value;
        $typeName = $type->getName();
        if (is_string($value) && $typeName === 'array') {

            try {
                $newValue = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw new RuntimeException('Invalid JSON in database '.$value);
            }
        }
        if (str_contains($typeName, '\\')) {
            /** @var class-string $objectClass */
            $objectClass = $typeName;

            $key = 'ReflectionClass'.$objectClass;
            if (isset(self::$reflectionCache[$key])) {
                /** @var ReflectionClass $typeClass */
                $typeClass = self::$reflectionCache[$key];
            } else {
                $typeClass = new ReflectionClass($objectClass);
                self::$reflectionCache[$key] = $typeClass;
            }

            if ((is_string($value) || is_int($value)) && $typeClass->isEnum()) {
                /** @var BackedEnum $typeName */
                /** @phpstan-ignore-next-line */
                $newValue = $typeName::from($value);
            }

            $interfaces = $typeClass->getInterfaces();
            if ((is_string($value) || is_int($value)) && $value !== '' && isset($interfaces[IdentifierInterface::class])) {
                /** @var IdentifierInterface $identity */
                /** @phpstan-ignore-next-line */
                $identity = $typeName;
                $newValue = $identity::create($value);
            }
            if ($typeClass->getName() === BaseCarbon::class) {
                Log::error('Carbon Class used directly');
                throw new RuntimeException('Carbon Class used directly');
            }

            if (is_numeric($value) && $typeClass->getName() === Carbon::class) {

                $newValue = Carbon::createFromTimestamp($value);
            } elseif (is_string($value) && $typeClass->getName() === Carbon::class) {

                $newValue = new Carbon(date($value));
            }
            if ($typeClass->implementsInterface(ValueObjectInterface::class)) {
                if ($value === '' && $typeClass->implementsInterface(IdentifierInterface::class)) {
                    return null;
                }
                /** @var ValueObjectInterface $valueObject */
                $valueObject = $typeClass->newInstanceWithoutConstructor();
                self::updateSingleValueObject($valueObject, $typeClass, $value);
                $newValue = $valueObject;
            }
        }

        return $newValue;
    }

    /**
     * Mengisi properti pertama sebuah Value Object dengan nilai yang telah
     * dikonversi sesuai tipe deklarasinya. Digunakan untuk Value Object
     * single-property (misal Identifier, primitive wrapper).
     *
     * @param  ValueObjectInterface  $valueObject  Instance Value Object target (dibuat tanpa constructor).
     * @param  ReflectionClass  $voClass  Reflection kelas Value Object.
     * @param  mixed  $value  Nilai mentah yang akan diset.
     *
     * @throws ReflectionException Bila reflection properti pertama gagal.
     */
    public static function updateSingleValueObject(ValueObjectInterface $valueObject, ReflectionClass $voClass, mixed $value): void
    {
        /** @var ReflectionNamedType|null $type */
        $type = $voClass->getProperties()[0]->getType();
        if ($type === null) {
            return;
        }
        $properties = $voClass->getProperties();
        $voProperty = $properties[0];
        $valueObject->{$voProperty->getName()} = self::calculateReflectionValue($type, $value);
    }

    /**
     * Mengisi properti tertentu (bukan yang pertama) dari sebuah Value Object
     * kompleks berdasarkan konvensi penamaan kolom database
     * `{voName}_{property}`.
     *
     * Contoh: kolom `address_city` untuk VO `address` akan mengisi properti
     * `city` pada Value Object tersebut.
     *
     * @param  string  $voName  Prefix nama Value Object pada kolom DB (mis. "address").
     * @param  string  $key  Nama kolom DB lengkap (mis. "address_city").
     * @param  ReflectionClass  $voClass  Reflection kelas Value Object target.
     * @param  mixed  $value  Nilai mentah dari database.
     * @param  string  $dbEngine  Nama db engine (disediakan untuk perluasan mendatang; belum dipakai).
     * @param  ValueObjectInterface  $valueObject  Instance Value Object target.
     *
     * @throws ReflectionException Bila reflection properti gagal.
     */
    protected static function updateNormalValueObject(string $voName, string $key, ReflectionClass $voClass, mixed $value, string $dbEngine, ValueObjectInterface $valueObject): void
    {
        $valueProperty = str_replace($voName.'_', '', $key);
        /** @var ReflectionNamedType|null $type */
        $type = $voClass->getProperty($valueProperty)->getType();
        if ($type === null) {
            return;
        }
        $valueObject->{$valueProperty} = self::calculateReflectionValue($type, $value);
    }
}
