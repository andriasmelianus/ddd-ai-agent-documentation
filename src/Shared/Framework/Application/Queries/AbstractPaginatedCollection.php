<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Application\Queries;

/**
 * Basis kelas mutable untuk koleksi hasil query yang dipaginasi.
 *
 * Kelas ini dipakai sebagai response objek pada sisi Query (CQRS) yang
 * bersifat writable dari sisi handler — properti dapat diisi bertahap
 * (item, total count, kemudian dihitung page-nya). Untuk representasi
 * immutable gunakan {@see PaginatedCollection}.
 *
 * @template T Tipe elemen di dalam koleksi.
 */
abstract class AbstractPaginatedCollection
{
    /** @var int|null Jumlah item per halaman (limit); null bila belum diset. */
    public ?int $pageSize = null;

    /** @var int|null Nomor halaman saat ini berbasis 1; null bila belum diset. */
    public ?int $page = null;

    /** @var int|null Total keseluruhan item lintas halaman; null bila belum dihitung. */
    public ?int $totalCount = null;

    /** @var array<int,T> Daftar item pada halaman saat ini. */
    public array $items = [];

    /**
     * Menghitung dan mengeset informasi pagination berdasarkan offset & limit
     * yang biasa dipakai pada query database (SQL LIMIT/OFFSET).
     *
     * Rumus perhitungan halaman:
     *  - pageSize = $limit
     *  - page     = floor($offset / $limit) + 1
     *
     * Jika {@see $totalCount} telah diisi sebelumnya, nilai page akan dibatasi
     * (clamped) agar tidak melebihi halaman terakhir yang tersedia.
     *
     * @param  int  $offset  Jumlah baris yang dilewati (mulai dari 0).
     * @param  int  $limit  Jumlah maksimum baris per halaman.
     */
    public function setPagination(int $offset, int $limit): void
    {
        $this->pageSize = $limit;
        $this->page = (int) ($offset / $limit) + 1;
        if ($this->totalCount !== null) {
            $this->page = min($this->page, (int) ceil($this->totalCount / $limit));
        }
    }
}
