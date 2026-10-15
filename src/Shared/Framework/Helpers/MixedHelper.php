<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Helpers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use JsonException;
use RuntimeException;
use Src\Shared\Framework\Domain\Enums\LanguageEnum;
use Src\Shared\Framework\Infrastructure\Logging\LoggerHelper;

use function array_key_exists;
use function count;
use function is_array;
use function is_bool;
use function is_numeric;
use function is_string;

/**
 * Helper utilitas untuk normalisasi dan konversi nilai bertipe campuran.
 *
 * Kelas ini menjadi pusat konversi defensif dari input `mixed` menuju tipe
 * yang lebih spesifik seperti string, int, float, bool, array, tanggal UNIX
 * timestamp, JSON string, dan localized string. Metode-metode di dalamnya
 * banyak dipakai saat membaca data dari request, JSON, database, integrasi,
 * atau payload eksternal yang tipenya tidak selalu konsisten.
 */
final class MixedHelper
{
    /**
     * Memeriksa apakah nilai string tampak seperti JSON object/array.
     *
     * Deteksi bersifat ringan: nilai dianggap JSON bila string mengandung
     * karakter `{` atau `[`. Metode ini tidak melakukan validasi JSON penuh.
     *
     * @param  mixed  $value  Nilai yang akan diperiksa.
     * @return bool True bila nilai berupa string yang tampak seperti JSON.
     */
    public static function isJson(mixed $value): bool
    {
        if (is_string($value) === false) {
            return false;
        }

        return Str::contains($value, ['{']) || Str::contains($value, ['[']);
    }

    /**
     * Membersihkan string dari spasi tepi dan mengubah string kosong menjadi null.
     *
     * @param  string|null  $string  String input yang akan dibersihkan.
     * @return string|null String bersih, atau null bila input null/kosong.
     */
    public static function cleanString(?string $string): ?string
    {
        if ($string === null) {
            return null;
        }

        $string = trim($string);
        if ($string === '') {
            return null;
        }

        return $string;
    }

    /**
     * Mengambil nilai string terlokalisasi berdasarkan bahasa.
     *
     * Input dapat berupa:
     *  - `null`, yang dikembalikan sebagai string kosong.
     *  - String biasa, yang langsung dikembalikan.
     *  - String JSON berisi map bahasa.
     *  - Array map bahasa seperti `['en' => '...', 'es' => '...']`.
     *
     * Jika bahasa yang diminta tidak tersedia dan bahasa adalah Spanish,
     * metode akan fallback ke English. Jika tetap tidak tersedia, `$onError`
     * dikembalikan bila tersedia; jika tidak, exception dilempar.
     *
     * @param  mixed  $mixed  Nilai localized string mentah.
     * @param  LanguageEnum  $language  Bahasa yang ingin diambil.
     * @param  string|null  $onError  Nilai fallback bila localized string invalid.
     * @return string Nilai string sesuai bahasa atau fallback.
     *
     * @throws RuntimeException Bila nilai tidak dapat dipetakan ke localized string.
     */
    public static function getLocalizedString(mixed $mixed, LanguageEnum $language, ?string $onError = null): string
    {
        if ($mixed === null) {
            return '';
        }

        if (is_string($mixed) && self::isJson($mixed) === false) {
            return $mixed;
        }

        // Check mixed json string
        if (is_string($mixed)) {
            if ($mixed === '') {
                return '';
            }
            $mixed = self::safeJsonDecode($mixed);
            if (is_array($mixed) === false) {
                return self::getString($mixed);
            }
        }
        if ($mixed === []) {
            return '';
        }
        if (isset($mixed[$language->value])) {
            return self::getString($mixed[$language->value]);
        }
        if (array_key_exists($language->value, $mixed)) { // null Value
            return '';
        }

        // fallback english
        if ($language === LanguageEnum::spanish) {
            if (isset($mixed[LanguageEnum::english->value])) {
                return self::getString($mixed[LanguageEnum::english->value]);
            }
        }

        if ($onError) {
            return $onError;
        }

        throw new RuntimeException('Invalid Localized String Value '.self::safeJson($mixed));
    }

    /**
     * Mengambil nilai string terlokalisasi atau null bila tidak tersedia.
     *
     * Berbeda dengan {@see self::getLocalizedString()}, metode ini tidak
     * melakukan fallback english dan tidak melempar exception ketika key bahasa
     * tidak ditemukan; hasilnya `null`.
     *
     * @param  mixed  $mixed  Nilai localized string mentah.
     * @param  LanguageEnum  $language  Bahasa yang ingin diambil.
     * @return string|null String sesuai bahasa, string kosong, atau null.
     */
    public static function getLocalizedStringOrNull(
        mixed $mixed,
        LanguageEnum $language,
    ): ?string {
        if (is_string($mixed) && self::isJson($mixed) === false) {
            return $mixed;
        }

        if ($mixed === null) {
            return null;
        }

        // Check mixed json string
        if (is_string($mixed)) {
            if ($mixed === '') {
                return '';
            }
            $mixed = self::safeJsonDecode($mixed);
            if (is_array($mixed) === false) {
                return self::getString($mixed);
            }
        }
        if ($mixed === []) {
            return '';
        }
        if (isset($mixed[$language->value])) {
            return self::getStringOrNull($mixed[$language->value]);
        }

        return null;
    }

    /**
     * Mengonversi nilai mixed menjadi string.
     *
     * String dan numerik dikonversi langsung; tipe lain akan di-encode ke JSON.
     *
     * @param  mixed  $mixedString  Nilai yang akan dikonversi.
     * @return string Representasi string dari nilai.
     */
    public static function getString(mixed $mixedString): string
    {
        if (is_string($mixedString)) {
            return $mixedString;
        }
        if (is_numeric($mixedString)) {
            return (string) $mixedString;
        }

        return self::safeJson($mixedString);
    }

    /**
     * Mengonversi nilai numerik/string menjadi float.
     *
     * @param  mixed  $value  Nilai yang akan dikonversi.
     * @return float Nilai float.
     *
     * @throws RuntimeException Bila nilai tidak dapat dikonversi menjadi float.
     */
    public static function getFloat(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        if (is_string($value)) {
            return (float) $value;
        }
        $errorValue = self::safeJson($value);

        throw new RuntimeException('Invalid Float Value '.$errorValue);
    }

    /**
     * Mengonversi nilai menjadi float dengan dukungan koma sebagai separator desimal.
     *
     * @param  mixed  $value  Nilai yang akan dikonversi.
     * @return float Nilai float hasil parsing.
     *
     * @throws RuntimeException Bila nilai tidak dapat dikonversi menjadi float.
     */
    public static function parseFloat(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        if (is_string($value)) {
            $value = str_replace(',', '.', $value);

            return (float) $value;
        }
        $errorValue = self::safeJson($value);

        throw new RuntimeException('Invalid Float Value '.$errorValue);
    }

    /**
     * Mengonversi nilai menjadi float nullable.
     *
     * @param  mixed  $value  Nilai yang akan dikonversi.
     * @return float|null Float, atau null bila input null.
     *
     * @throws RuntimeException Bila nilai tidak dapat dikonversi menjadi float.
     */
    public static function getFloatOrNull(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        if (is_numeric($value)) {
            if ($value === 0) {
                return 0.0;
            }

            return (float) $value;
        }
        if (is_string($value)) {
            if ($value === '0') {
                return 0.0;
            }

            return (float) $value;
        }
        throw new RuntimeException('Invalid Float Value '.self::safeJson($value));
    }

    /**
     * Mengonversi nilai mixed menjadi array.
     *
     * String akan dianggap sebagai JSON dan di-decode. Null dikembalikan sebagai
     * array kosong. String JSON yang tidak menghasilkan array juga dikembalikan
     * sebagai array kosong.
     *
     * @param  mixed  $mixed  Nilai yang akan dikonversi.
     * @return array<string|int,mixed>|array<mixed> Array hasil konversi.
     *
     * @throws RuntimeException Bila nilai bukan array/string/null yang valid.
     */
    public static function getArray(mixed $mixed): array
    {
        if (is_array($mixed)) {
            return $mixed;
        }
        if (is_string($mixed)) {
            $array = self::safeJsonDecode($mixed);
            if (is_array($array) === false) {
                return [];
            }

            return $array;
        }
        if ($mixed === null) {
            return [];
        }
        throw new RuntimeException('Invalid value array '.self::safeJson($mixed));
    }

    /**
     * Mengonversi nilai mixed menjadi array nullable.
     *
     * @param  mixed  $mixed  Nilai yang akan dikonversi.
     * @return array<string|int,mixed>|array<mixed>|null Array hasil konversi atau null.
     */
    public static function getArrayOrNull(mixed $mixed): ?array
    {
        if ($mixed === null) {
            return null;
        }

        return self::getArray($mixed);
    }

    /**
     * Mengekstrak string dari nilai yang mungkin berbentuk JSON/array tunggal.
     *
     * Jika input adalah JSON string, nilai akan di-decode lebih dulu. Jika input
     * array dengan satu elemen, metode ini akan mengekstrak elemen tersebut
     * secara rekursif.
     *
     * @param  mixed  $mixed  Nilai sumber.
     * @return string String hasil ekstraksi.
     *
     * @throws RuntimeException Bila nilai tidak dapat diekstrak menjadi string.
     */
    public static function extractString(mixed $mixed): string
    {
        if (is_string($mixed) && self::isJson($mixed)) {
            $mixed = self::safeJsonDecode($mixed);
        }
        if (is_string($mixed)) {
            return $mixed;
        }
        if (is_numeric($mixed)) {
            return (string) $mixed;
        }
        if (is_array($mixed) && count($mixed) === 1) {
            return self::extractString(array_values($mixed)[0]);
        }
        throw new RuntimeException('Invalid String Value '.self::safeJson($mixed));
    }

    /**
     * Mengekstrak string nullable dari nilai yang mungkin berbentuk JSON/array tunggal.
     *
     * @param  mixed  $mixed  Nilai sumber.
     * @return string|null String hasil ekstraksi, atau null bila input null.
     *
     * @throws RuntimeException Bila nilai tidak dapat diekstrak menjadi string.
     */
    public static function extractStringOrNull(mixed $mixed): ?string
    {
        if ($mixed === null) {
            return null;
        }

        if (is_string($mixed) && self::isJson($mixed)) {
            $mixed = self::safeJsonDecode($mixed);
        }
        if (is_string($mixed)) {
            return $mixed;
        }
        if (is_numeric($mixed)) {
            return (string) $mixed;
        }
        if (is_array($mixed) && count($mixed) === 1) {
            return self::extractStringOrNull(array_values($mixed)[0]);
        }
        throw new RuntimeException('Invalid String Value '.self::safeJson($mixed));
    }

    /**
     * Mengekstrak integer dari nilai yang mungkin berbentuk JSON/array tunggal.
     *
     * @param  mixed  $value  Nilai sumber.
     * @return int Integer hasil ekstraksi.
     *
     * @throws RuntimeException Bila nilai tidak dapat diekstrak menjadi integer.
     */
    public static function extractInt(mixed $value): int
    {
        if (is_string($value) && self::isJson($value)) {
            $value = self::safeJsonDecode($value);
        }
        if (is_numeric($value)) {
            return (int) $value;
        }
        if (is_string($value)) {
            return (int) $value;
        }
        if (is_array($value) && count($value) === 1) {
            return self::extractInt(array_values($value)[0]);
        }
        throw new RuntimeException('Invalid Int Value '.self::safeJson($value));
    }

    /**
     * Mengonversi nilai mixed menjadi integer.
     *
     * Untuk array, metode ini mencoba mengambil elemen pertama bila elemen
     * tersebut numeric/string. Nilai invalid akan dicatat dan dilempar sebagai exception.
     *
     * @param  mixed  $value  Nilai sumber.
     * @return int Integer hasil konversi.
     *
     * @throws RuntimeException Bila nilai tidak dapat dikonversi menjadi integer.
     */
    public static function getInt(mixed $value): int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }
        if (is_string($value)) {
            return (int) $value;
        }
        if (is_array($value) && count($value) > 0) {
            if (is_numeric($value[0])) {
                return (int) $value[0];
            }

            if (is_string($value[0])) {
                return (int) $value[0];
            }
        }
        LoggerHelper::logException(
            new RuntimeException('Invalid Int Value '.self::safeJson($value))
        );
        throw new RuntimeException('Invalid Int Value '.self::safeJson($value));
    }

    /**
     * Mengonversi nilai mixed menjadi integer nullable.
     *
     * String kosong dikembalikan sebagai null.
     *
     * @param  mixed  $value  Nilai sumber.
     * @return int|null Integer hasil konversi atau null.
     *
     * @throws RuntimeException Bila nilai tidak dapat dikonversi menjadi integer.
     */
    public static function getIntOrNull(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }
        if (is_string($value)) {
            if ($value === '') {
                return null;
            }

            return (int) $value;
        }
        LoggerHelper::logException(
            new RuntimeException('Invalid Int Value '.self::safeJson($value))
        );
        throw new RuntimeException('Invalid Int Value '.self::safeJson($value));
    }

    /**
     * Mengonversi nilai mixed menjadi boolean.
     *
     * String yang dianggap `true` adalah `true`, `yes`, dan `1`; string lain
     * dikembalikan sebagai false.
     *
     * @param  mixed  $value  Nilai sumber.
     * @return bool Boolean hasil konversi.
     *
     * @throws RuntimeException Bila nilai tidak dapat dikonversi menjadi boolean.
     */
    public static function getBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (bool) $value;
        }

        if (is_string($value)) {
            return $value === 'true' || $value === 'yes' || $value === 'on' || $value === '1';
        }
        throw new RuntimeException('Invalid Bool Value '.self::safeJson($value));
    }

    /**
     * Mengonversi nilai mixed menjadi boolean nullable.
     *
     * @param  mixed  $value  Nilai sumber.
     * @return bool|null Boolean hasil konversi atau null.
     *
     * @throws RuntimeException Bila nilai tidak dapat dikonversi menjadi boolean.
     */
    public static function getBooleanOrNull(mixed $value): ?bool
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        if (is_string($value)) {
            return $value === 'true' || $value === 'yes' || $value === '1';
        }
        throw new RuntimeException('Invalid Bool Value '.self::safeJson($value));
    }

    /**
     * Mengonversi nilai mixed menjadi string nullable.
     *
     * Nilai `null` atau string literal `"null"` dikembalikan sebagai null.
     *
     * @param  mixed  $mixedString  Nilai sumber.
     * @return string|null String hasil konversi atau null.
     */
    public static function getStringOrNull(mixed $mixedString): ?string
    {
        if ($mixedString === null || $mixedString === 'null') {
            return null;
        }
        if (is_string($mixedString)) {
            return $mixedString;
        }
        if (is_numeric($mixedString)) {
            return (string) $mixedString;
        }

        return self::safeJson($mixedString);
    }

    /**
     * Mengonversi nilai mixed menjadi array string.
     *
     * @param  mixed  $mixed  Nilai array/JSON sumber.
     * @return string[] Array string hasil konversi per item.
     */
    public static function getStringArray(mixed $mixed): array
    {
        $array = self::getArray($mixed);

        return array_map(
            static function ($item) {
                return self::getString($item);
            },
            $array
        );
    }

    /**
     * Mengonversi nilai mixed menjadi array string nullable.
     *
     * @param  mixed  $mixed  Nilai array/JSON sumber.
     * @return array<string>|null Array string, atau null bila input null.
     */
    public static function getStringArrayOrNull(mixed $mixed): ?array
    {
        if ($mixed === null) {
            return null;
        }
        $array = self::getArray($mixed);

        return array_map(
            static function ($item) {
                return self::getString($item);
            },
            $array
        );
    }

    /**
     * Mengonversi nilai mixed menjadi array integer.
     *
     * @param  mixed  $mixed  Nilai array/JSON sumber.
     * @return int[] Array integer hasil konversi per item.
     */
    public static function getIntArray(mixed $mixed): array
    {
        $array = self::getArray($mixed);

        return array_map(
            static function ($item) {
                return self::getInt($item);
            },
            $array
        );
    }

    /**
     * Mengonversi nilai tanggal menjadi UNIX timestamp nullable.
     *
     * Nama metode mempertahankan ejaan existing (`getDaterOrNull`) untuk menjaga
     * kompatibilitas pemanggil lama.
     *
     * @param  mixed  $value  Nilai numeric/string tanggal.
     * @return int|null UNIX timestamp, atau null bila input null / string tidak valid.
     *
     * @throws RuntimeException Bila nilai bukan numeric/string/null.
     */
    public static function getDaterOrNull(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }
        if (is_numeric($value)) {
            return (int) $value;
        }
        if (is_string($value)) {
            return strtotime($value) ?: null;
        }
        throw new RuntimeException('Invalid Date Value '.self::safeJson($value));
    }

    /**
     * Mengonversi nilai tanggal menjadi UNIX timestamp.
     *
     * Mendukung numeric timestamp, string yang dapat diparse oleh `strtotime`,
     * dan instance {@see Carbon}.
     *
     * @param  mixed  $value  Nilai tanggal sumber.
     * @return int UNIX timestamp.
     *
     * @throws RuntimeException Bila nilai tidak dapat dikonversi menjadi tanggal.
     */
    public static function getDate(mixed $value): int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }
        if (is_string($value)) {
            $date = strtotime($value);
            if ($date === false) {
                throw new RuntimeException('Invalid Date Value '.self::safeJson($value));
            }
        }
        if ($value instanceof Carbon) {
            return (int) $value->timestamp;
        }
        throw new RuntimeException('Invalid Date Value '.self::safeJson($value));
    }

    /**
     * Mengonversi nilai menjadi array non-kosong tanpa nilai null/string kosong.
     *
     * Object akan diubah lebih dulu menjadi JSON lalu di-decode menjadi array.
     * Nilai `null`, JSON array kosong (`[]`), atau JSON object kosong (`{}`)
     * dikembalikan sebagai null.
     *
     * @param  mixed  $value  Nilai sumber.
     * @return array<string|int,mixed>|array<mixed>|null Array yang sudah dibersihkan atau null.
     */
    public static function getNonEmptyArrayOrNull(mixed $value): ?array
    {
        if (is_object($value)) {
            $value = self::safeJsonDecode(self::safeJson($value));
        }
        if ($value === null) {
            return null;
        }
        if ($value === '[]' || $value === '{}') {
            return null;
        }
        // Remove null values array
        $array = self::getArray($value);
        $array = array_filter($array, static function ($value) {
            return $value !== null && $value !== '';
        });

        return $array;
    }

    /**
     * Menormalisasi berat ke satuan yang lebih masuk akal berdasarkan rentang angka.
     *
     * Metode ini menangani data berat yang sering datang dengan skala berbeda
     * (mis. gram/decimal tersimpan tanpa separator) dengan membagi nilai sesuai
     * rentang dan pola angka pertama.
     *
     * @param  float|null  $weight  Berat mentah.
     * @return float|null Berat yang sudah dinormalisasi, atau null.
     *
     * @throws RuntimeException Bila berat berada di luar rentang yang didukung.
     */
    public static function getWeightNormalizedOrNull(?float $weight): ?float
    {
        if ($weight === null) {
            return null;
        }

        if ($weight <= 250) {
            return $weight;
        }

        if ($weight <= 1000) {
            return $weight / 10;
        }

        if ($weight <= 10000) {
            if (str_starts_with(self::getString($weight), '1')) {
                return $weight / 10;
            }

            return $weight / 100;
        }

        if ($weight <= 100000) {
            if (str_starts_with(self::getString($weight), '1')) {
                return $weight / 100;
            }

            return $weight / 1000;
        }

        if ($weight <= 1000000) {
            if (str_starts_with(self::getString($weight), '1')) {
                return $weight / 1000;
            }

            return $weight / 10000;
        }

        if ($weight <= 9999999) {
            if (str_starts_with(self::getString($weight), '1')) {
                return $weight / 10000;
            }

            return $weight / 100000;
        }

        throw new RuntimeException('Invalid Weight Value '.$weight);
    }

    /**
     * Mengonversi string JSON/array menjadi array float.
     *
     * @param  string  $mixed  String JSON yang merepresentasikan array.
     * @return array<float> Array float hasil konversi.
     */
    public static function getFloatArray(string $mixed): array
    {
        $array = self::getArray($mixed);

        return array_map(
            static function ($item) {
                return self::getFloat($item);
            },
            $array
        );
    }

    /**
     * Meng-encode nilai menjadi JSON string dengan penanganan error aman.
     *
     * Jika encoding gagal, exception dicatat dan string kosong dikembalikan.
     *
     * @param  mixed  $item  Nilai yang akan di-encode.
     * @return string JSON string, atau string kosong bila gagal.
     */
    public static function safeJson(mixed $item): string
    {
        try {
            return json_encode($item, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            LoggerHelper::logException($e);
        }

        return '';
    }

    /**
     * Meng-encode nilai menjadi JSON string nullable.
     *
     * Input null langsung menghasilkan null. Error encoding dicatat dan
     * dikembalikan sebagai string kosong.
     *
     * @param  mixed  $item  Nilai yang akan di-encode.
     * @return string|null JSON string, null, atau string kosong bila gagal.
     */
    public static function safeJsonOrNull(mixed $item): ?string
    {
        if ($item === null) {
            return null;
        }
        try {
            return json_encode($item, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            LoggerHelper::logException($e);
        }

        return '';
    }

    /**
     * Meng-encode nilai menjadi JSON setelah menghapus nilai kosong.
     *
     * Nilai `null` dan string kosong pada array hasil normalisasi akan dibuang.
     * Jika input null, hasilnya null. Error JSON dicatat dan string kosong
     * dikembalikan.
     *
     * @param  mixed  $item  Nilai yang akan dibersihkan dan di-encode.
     * @return string|null JSON string bersih, null, atau string kosong bila gagal.
     */
    public static function safeCleanJsonOrNull(mixed $item): ?string
    {
        if ($item === null) {
            return null;
        }
        try {
            /** @var array<mixed> $array */
            $array = json_decode(json_encode($item, JSON_THROW_ON_ERROR), true);
            $cleanedArray = array_filter($array, static function ($value) {
                return $value !== null && $value !== '';
            });

            return json_encode($cleanedArray, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            LoggerHelper::logException($e);
        }

        return '';
    }

    /**
     * Decode JSON string dengan penanganan error aman.
     *
     * String kosong dikembalikan apa adanya. Bila decode gagal, exception
     * dicatat dan string kosong dikembalikan.
     *
     * @param  string  $item  JSON string yang akan di-decode.
     * @return mixed Hasil decode, string kosong untuk input kosong/error.
     */
    public static function safeJsonDecode(string $item): mixed
    {
        try {
            if ($item === '') {
                return '';
            }

            return json_decode($item, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            LoggerHelper::logException(new RuntimeException('Invalid json '.$item.' '.$e->getMessage()));
        }

        return '';
    }
}
