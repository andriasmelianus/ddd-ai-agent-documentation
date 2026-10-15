<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Helpers;

use function count;
use function is_array;

/**
 * AssertHelper.
 *
 * Kelas fluent assertion (rantai validasi) yang digunakan untuk memvalidasi
 * satu-per-satu variabel dan mengakumulasi pesan error tanpa langsung
 * melempar exception. Alur pemakaian umum:
 *
 * ```
 * $errors = (new AssertHelper())
 *     ->variable('name', $name)->required()
 *     ->variable('age',  $age)->required()->gte(0)->lte(120)
 *     ->getErrors();
 * ```
 *
 * Semua metode assertion mengembalikan `$this`, sehingga memungkinkan gaya
 * penulisan berantai (fluent). Kelas ini memelihara state internal berupa
 * "variabel aktif" (`$name` + `$value`) sehingga metode assertion berikutnya
 * beroperasi pada variabel yang sama sampai `variable()` dipanggil ulang.
 */
class AssertHelper
{
    /**
     * @param  array<string>  $errors  Akumulator pesan error yang telah terkumpul.
     * @param  string  $name  Nama variabel aktif yang sedang divalidasi.
     * @param  mixed  $value  Nilai variabel aktif yang sedang divalidasi.
     */
    public function __construct(private array $errors = [], private string $name = '', private mixed $value = '') {}

    /**
     * Menambahkan pesan error secara manual ke akumulator.
     *
     * @param  string  $error  Pesan error yang akan ditambahkan.
     * @return self Instance sekarang untuk chaining.
     */
    public function addError(string $error): self
    {
        $this->errors[] = $error;

        return $this;
    }

    /**
     * Menetapkan variabel yang akan divalidasi oleh metode-metode assertion
     * selanjutnya.
     *
     * @param  string  $name  Nama variabel (dipakai di dalam pesan error).
     * @param  mixed  $value  Nilai variabel.
     * @return self Instance sekarang untuk chaining.
     */
    public function variable(string $name, mixed $value): self
    {
        $this->name = $name;
        $this->value = $value;

        return $this;
    }

    /**
     * Memvalidasi bahwa variabel aktif tidak kosong.
     *
     * Nilai dianggap kosong bila:
     * - String kosong `''`, `null`, atau string literal `'null'`.
     * - Array kosong.
     *
     * @return self Instance sekarang untuk chaining.
     */
    public function required(): self
    {
        if ($this->value === '' || $this->value === null || $this->value === 'null') {
            $this->errors[] = $this->name.' is required';
        }

        if (is_array($this->value) && count($this->value) === 0) {
            $this->errors[] = $this->name.' is required';
        }

        return $this;
    }

    /**
     * Memvalidasi bahwa nilai integer variabel aktif >= `$count`.
     *
     * @param  int  $count  Ambang batas minimum (inklusif).
     * @return self Instance sekarang untuk chaining.
     */
    public function gte(int $count): self
    {
        if (MixedHelper::getInt($this->value) < $count) {
            $this->errors[] = $this->name.' must be greater than or equal to '.$count;
        }

        return $this;
    }

    /**
     * Memvalidasi bahwa nilai integer variabel aktif berada dalam rentang
     * `[$min, $max]` (inklusif).
     *
     * @param  int  $min  Nilai minimum yang diperbolehkan.
     * @param  int  $max  Nilai maksimum yang diperbolehkan.
     * @return self Instance sekarang untuk chaining.
     */
    public function between(int $min, int $max): self
    {
        if (MixedHelper::getInt($this->value) < $min || MixedHelper::getInt($this->value) > $max) {
            $this->errors[] = $this->name.' debe estar entre '.$min.' y '.$max;
        }

        return $this;
    }

    /**
     * Memvalidasi bahwa nilai integer variabel aktif <= `$count`.
     *
     * @param  int  $count  Ambang batas maksimum (inklusif).
     * @return self Instance sekarang untuk chaining.
     */
    public function lte(int $count): self
    {
        if (MixedHelper::getInt($this->value) > $count) {
            $this->errors[] = $this->name.' must be lower than or equal to '.$count;
        }

        return $this;
    }

    /**
     * Memvalidasi bahwa nilai integer variabel aktif sama persis dengan
     * `$count`.
     *
     * @param  int  $count  Nilai yang diharapkan.
     * @return self Instance sekarang untuk chaining.
     */
    public function eq(int $count): self
    {
        if (MixedHelper::getInt($this->value) !== $count) {
            $this->errors[] = $this->name.' must be equal to '.$count;
        }

        return $this;
    }

    /**
     * Memvalidasi bahwa nilai array variabel aktif memiliki jumlah elemen
     * >= `$count`. Bila variabel bukan array, assertion diabaikan.
     *
     * @param  int  $count  Jumlah elemen minimum yang diharapkan.
     * @return self Instance sekarang untuk chaining.
     */
    public function arrayGTECount(int $count): self
    {
        if (is_array($this->value) && count($this->value) < $count) {
            $this->errors[] = $this->name.' must contain more than '.$count;
        }

        return $this;
    }

    /**
     * Memvalidasi bahwa nilai array variabel aktif memiliki jumlah elemen
     * sama persis dengan `$count`. Bila variabel bukan array, assertion
     * diabaikan.
     *
     * @param  int  $count  Jumlah elemen yang diharapkan.
     * @return self Instance sekarang untuk chaining.
     */
    public function arrayEQCount(int $count): self
    {
        if (is_array($this->value) && count($this->value) !== $count) {
            $this->errors[] = $this->name.' must be equal to '.$count;
        }

        return $this;
    }

    /**
     * Mengembalikan seluruh pesan error yang telah terkumpul.
     *
     * @return array<string> Daftar pesan error (kosong bila semua valid).
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
