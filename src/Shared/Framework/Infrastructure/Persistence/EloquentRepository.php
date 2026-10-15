<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;
use Src\Shared\Framework\Domain\Entities\BaseEntity;
use Src\Shared\Framework\Domain\ValueObjects\AutoIncrementIdentifier;

/**
 * Kelas dasar (abstract) untuk repository berbasis Eloquent.
 *
 * Menyediakan operasi persistence umum yang sering dibutuhkan oleh repository
 * konkret di lapisan Infrastructure, seperti bulk insert, bulk upsert, dan
 * upsert entitas tunggal berdasarkan state `isNew` pada {@see BaseEntity}.
 *
 * Metode-metode di sini menggunakan `ClassName::query()` alih-alih dependensi
 * konkret pada model tertentu, sehingga dapat dipakai kembali oleh repository
 * turunan tanpa duplikasi kode. Nama kelas model diberikan sebagai parameter
 * (`class-string<Model>`) untuk mempertahankan fleksibilitas.
 *
 * Kelas ini bersifat `abstract` — harus diperluas oleh repository domain
 * spesifik (mis. `PartnerRepository`, `OrderRepository`).
 */
abstract class EloquentRepository
{
    /**
     * Melakukan bulk insert ke tabel model target tanpa mengaktifkan event Eloquent.
     *
     * Digunakan untuk skenario impor data massal (mis. seeder/populator) di mana
     * performa lebih diutamakan daripada trigger event `creating`/`created`.
     *
     * @param  class-string<Model>  $ARClassName  Nama kelas Model Eloquent target.
     * @param  array<array<string, mixed>>  $data  Array baris yang akan di-insert (setiap elemen berisi kolom → nilai).
     */
    public function insertBulk(string $ARClassName, array $data): void
    {
        /** @var Model $ARClass */
        /** @phpstan-ignore-next-line */
        $ARClass = $ARClassName;
        $ARClass::query()->insert($data);
    }

    /**
     * Melakukan bulk upsert (INSERT ... ON DUPLICATE KEY UPDATE / MERGE) pada tabel model target.
     *
     * @param  string  $ARClassName  Nama kelas Model Eloquent target.
     * @param  array<mixed>  $data  Array baris yang akan di-upsert.
     * @param  array<mixed>  $keys  Kolom yang dianggap unik untuk mendeteksi duplikasi.
     * @param  array<string>|null  $columns  Kolom yang akan di-update jika baris sudah ada. `null` = update seluruh kolom.
     */
    public function upsertBulk(string $ARClassName, array $data, array $keys, ?array $columns = null): void
    {
        /** @var Model $ARClass */
        /** @phpstan-ignore-next-line */
        $ARClass = $ARClassName;
        $ARClass::query()->upsert(values: $data, uniqueBy: $keys, update: $columns);
    }

    /**
     * Menyimpan sebuah {@see BaseEntity} ke tabel model target (insert atau update).
     *
     * Perilaku:
     *  - Jika `$baseEntity->isNew()` bernilai `true`, dilakukan `create($data)`.
     *    Bila entitas menggunakan `AutoIncrementIdentifier`, ID auto-increment
     *    hasil insert akan di-set kembali ke `$baseEntity->id` via
     *    {@see AutoIncrementIdentifier::setAutoIncrement()}.
     *  - Jika bukan entitas baru, sistem mencoba `find($id)`; bila record
     *    ditemukan, dilakukan `fill($data)` + `save()`. Jika tidak ditemukan,
     *    dilakukan `create($data)` sebagai fallback.
     *
     * @param  BaseEntity  $baseEntity  Entitas domain yang akan disimpan.
     * @param  class-string<Model>  $ARClassName  Nama kelas Model Eloquent target.
     * @param  array<string, mixed>  $data  Payload atribut yang akan disimpan.
     *
     * @throws RuntimeException Bila ID auto-increment tidak dapat diperoleh setelah insert (getKey() === -1).
     */
    public function upsert(BaseEntity $baseEntity, string $ARClassName, array $data): void
    {
        /** @var Model $ARClass */
        /** @phpstan-ignore-next-line */
        $ARClass = $ARClassName;
        if ($baseEntity->isNew()) {
            /** @var Model $newValue */
            $newValue = $ARClass::query()->create($data);
            if (isset($baseEntity->id) && $baseEntity->id instanceof AutoIncrementIdentifier) {

                /** @var int $id */
                $id = $newValue->getKey();
                if ($id === -1) {

                    throw new RuntimeException('Error getting autoincrement id');
                }
                $baseEntity->id->setAutoIncrement($id);
            }
        } else {
            /** @var Model|null $ar */
            $ar = $ARClass::query()->find($baseEntity->id->getValue());
            if ($ar === null) {
                $ARClass::query()->create($data);
            } else {
                $ar->fill($data);
                $ar->save();
            }
        }
    }
}
