<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

use function array_key_exists;

/**
 * CacheHelper.
 *
 * Kelas utilitas untuk menjalankan callback tepat satu kali per kunci
 * dengan dua mode caching:
 *
 * 1. **In-memory (per request)**: bila `$ttl === 0`, hasil callback disimpan
 *    di properti statis {@see self::$cache} dan hidup selama request PHP
 *    berlangsung.
 * 2. **Persistent (Laravel Cache)**: bila `$ttl > 0`, hasil disimpan via
 *    facade {@see Cache::remember()} dengan TTL dalam detik.
 *
 * Kunci cache secara otomatis diberi prefix versi berdasarkan hash commit
 * (config `app.git_commit`) sehingga rilis baru tidak salah membaca data
 * lama, terutama saat rolling deployment.
 *
 * Kelas ini juga memiliki mekanisme resilien untuk memulihkan error
 * deserialisasi pada cache persistent (mis. karena kelas berubah antar
 * rilis) — entri bermasalah akan dihapus dan callback dijalankan ulang.
 */
final class CacheHelper
{
    /** @var array<string,mixed> Penyimpanan cache in-memory per request. */
    private static array $cache = [];

    /**
     * Mengembalikan nilai hasil `$callback` dengan jaminan hanya dieksekusi
     * satu kali per `$key` (per request atau per TTL).
     *
     * Perilaku bergantung pada `$ttl`:
     * - `$ttl === 0` → cache in-memory (hidup selama request PHP).
     * - `$ttl > 0`   → cache persistent via Laravel Cache selama `$ttl` detik.
     *
     * Bila terjadi kegagalan deserialisasi dari cache persistent (biasanya
     * karena struktur kelas berubah antar rilis), entri yang bermasalah
     * dihapus otomatis dan callback dijalankan ulang.
     *
     * @param  string  $key  Kunci cache logis (akan diberi prefix versi).
     * @param  callable  $callback  Callback yang menghasilkan nilai bila cache miss.
     * @param  int  $ttl  Time-to-live dalam detik; 0 untuk in-memory saja.
     * @return mixed Nilai dari cache atau hasil eksekusi callback.
     *
     * @throws Throwable Bila error dari cache bukan error deserialisasi.
     */
    public static function onceByKey(string $key, callable $callback, int $ttl = 0): mixed
    {
        $versionedKey = self::addVersionPrefix($key);

        if ($ttl === 0) {
            if (array_key_exists($versionedKey, self::$cache)) {
                return self::$cache[$versionedKey];
            }

            $value = $callback();
            self::$cache[$versionedKey] = $value;

            return $value;
        }

        try {
            return Cache::remember($versionedKey, $ttl, static function () use ($callback) {
                return $callback();
            });
        } catch (Throwable $e) {
            // Handle deserialization errors during rolling deployments
            if (self::isSerializationError($e)) {
                Log::warning('Cache deserialization error, clearing key', [
                    'key' => $versionedKey,
                    'error' => $e->getMessage(),
                ]);

                Cache::forget($versionedKey);

                return Cache::remember($versionedKey, $ttl, static function () use ($callback) {
                    return $callback();
                });
            }

            throw $e;
        }
    }

    /**
     * Menambahkan prefix versi (`v:{shortCommit}:`) ke kunci agar entri
     * cache antar rilis tidak saling tumpang tindih.
     *
     * Hanya 8 karakter pertama dari `app.git_commit` yang digunakan untuk
     * menjaga kunci tetap ringkas. Bila konfigurasi tidak berupa string,
     * digunakan fallback `'dev'`.
     *
     * @param  string  $key  Kunci cache asli tanpa prefix.
     * @return string Kunci cache dengan prefix versi.
     */
    private static function addVersionPrefix(string $key): string
    {
        $gitCommit = config('app.git_commit', 'dev');
        // Use short hash (first 8 chars) to keep keys readable
        $version = is_string($gitCommit) ? substr($gitCommit, 0, 8) : 'dev';

        return "v:{$version}:{$key}";
    }

    /**
     * Mendeteksi apakah exception yang dilemparkan oleh cache backend
     * berasal dari kegagalan proses deserialisasi (mis. kelas hilang atau
     * berubah).
     *
     * Heuristik memeriksa keberadaan pola pesan seperti:
     * - `"unserialize"`
     * - `"Error at offset"`
     * - `"__PHP_Incomplete_Class"`
     * - Kombinasi `"Class"` + `"not found"`
     *
     * @param  Throwable  $e  Exception yang akan diperiksa.
     * @return bool True bila termasuk error deserialisasi.
     */
    private static function isSerializationError(Throwable $e): bool
    {
        $message = $e->getMessage();

        return str_contains($message, 'unserialize') ||
            str_contains($message, 'Error at offset') ||
            str_contains($message, '__PHP_Incomplete_Class') ||
            (str_contains($message, 'Class') && str_contains($message, 'not found'));
    }

    /**
     * Mengosongkan seluruh cache in-memory (tidak mempengaruhi cache
     * persistent). Berguna terutama pada skenario testing.
     */
    public static function reset(): void
    {
        self::$cache = [];
    }
}
