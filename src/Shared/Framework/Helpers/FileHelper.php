<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Helpers;

/**
 * FileHelper.
 *
 * Kumpulan helper statis untuk manipulasi path file/URL. Menyediakan
 * fungsi-fungsi kecil yang membantu membangun path yang konsisten dan bebas
 * dari duplikasi separator.
 */
final class FileHelper
{
    /**
     * Menggabungkan `host` (atau base path) dengan `path` menjadi satu string
     * yang dipisahkan tepat satu karakter `/`.
     *
     * Metode ini menghapus seluruh separator `/` berlebih di akhir `host`
     * dan di awal `path`, sehingga hasil akhir selalu berbentuk
     * `"{host}/{path}"` tanpa ada garis miring ganda. Berguna untuk membangun
     * URL, path storage, atau path publik dari beberapa komponen dinamis.
     *
     * @param  string  $host  Bagian awal path (mis. base URL atau direktori root).
     * @param  string  $path  Bagian akhir path (mis. relative path atau nama file).
     * @return string Gabungan path dengan tepat satu separator `/`.
     */
    public static function fixPath(string $host, string $path): string
    {

        do {
            if ($host[-1] === '/') {
                $host = substr($host, 0, -1);
            }
            if ($path[0] === '/') {

                $path = substr($path, 1);
            }
        } while ($host[-1] === '/' || $path[0] === '/');

        return $host.'/'.$path;
    }
}
