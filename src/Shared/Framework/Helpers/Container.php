<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Helpers;

use Illuminate\Support\Facades\App;

/**
 * Container.
 *
 * Pembungkus tipis (thin wrapper) di atas Service Container Laravel yang
 * memberikan API type-safe untuk mengambil instance objek berdasarkan
 * class-string. Bertujuan untuk memusatkan resolusi dependensi dan membantu
 * analisis statis (PHPStan/Psalm) dengan bantuan generic template `T`.
 *
 * @template T of object
 */
class Container
{
    /**
     * Mengambil instance objek dari service container Laravel berdasarkan
     * identifier kelas (class-string) yang diberikan.
     *
     * Fungsi ini menjadi jalur tunggal untuk resolusi dependency, sehingga
     * memudahkan penggantian implementasi container di masa depan bila
     * dibutuhkan. Nilai balik dijamin bertipe objek dan konsisten dengan
     * class-string yang diminta.
     *
     * @param  class-string<T>  $id  Nama kelas/antarmuka yang akan diresolusi.
     * @return T Instance objek hasil resolusi container.
     */
    public static function getObjectInstance(string $id): object
    {
        /** @var T $object */
        $object = App::make($id);

        return $object;
    }
}
