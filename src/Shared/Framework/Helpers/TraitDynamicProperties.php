<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Helpers;

use RuntimeException;

/**
 * TraitDynamicProperties.
 *
 * Trait yang menambahkan akses dinamis ke properti objek melalui magic
 * methods `__get`, `__isset`, dan `__set`.
 *
 * Trait ini secara sengaja membatasi akses hanya pada properti yang
 * benar-benar dideklarasikan pada kelas pengguna (`property_exists`),
 * sehingga tidak menciptakan dynamic properties baru — konsisten dengan
 * kebijakan `#[\AllowDynamicProperties]` di PHP 8.2+.
 *
 * Berguna untuk kelas yang ingin mengekspos properti `private`/`protected`
 * secara read-only atau semi-controlled tanpa perlu menulis getter/setter
 * per properti.
 */
trait TraitDynamicProperties
{
    /**
     * Magic getter: mengembalikan nilai properti `$name` bila properti
     * tersebut dideklarasikan pada objek. Jika tidak ada, akan melempar
     * {@see RuntimeException}.
     *
     * @param  string  $name  Nama properti yang dibaca.
     * @return mixed Nilai properti yang bersangkutan.
     *
     * @throws RuntimeException Bila properti tidak dideklarasikan di kelas.
     */
    public function __get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new RuntimeException("Property {$name} does not exist");
    }

    /**
     * Magic `isset()`: mengembalikan `true` bila properti `$name`
     * dideklarasikan pada objek.
     *
     * Catatan: metode ini memeriksa keberadaan deklarasi properti, bukan
     * apakah nilainya `null` atau bukan.
     *
     * @param  string  $name  Nama properti yang diperiksa.
     * @return bool True bila properti dideklarasikan.
     */
    public function __isset(string $name): bool
    {
        return property_exists($this, $name);
    }

    /**
     * Magic setter: menetapkan nilai properti `$name` hanya bila properti
     * tersebut telah dideklarasikan pada objek. Bila tidak, operasi diabaikan
     * secara diam-diam (silent no-op) untuk mencegah pembuatan dynamic
     * property tak terdeklarasi.
     *
     * @param  string  $name  Nama properti yang ditulis.
     * @param  mixed  $value  Nilai baru yang akan ditetapkan.
     */
    public function __set(string $name, mixed $value): void
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }
}
