<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Application\Queries;

/**
 * DTO immutable yang merepresentasikan hasil query dengan pagination.
 *
 * Berbeda dengan {@see AbstractPaginatedCollection}, kelas ini bersifat
 * `final readonly` sehingga aman dipakai sebagai payload lintas layer
 * (Application -> Presentation) tanpa risiko mutasi.
 *
 * @template T Tipe elemen di dalam koleksi.
 */
final readonly class PaginatedCollection
{
    /**
     * @param  array<int,T>  $items  Daftar item pada halaman saat ini.
     * @param  int  $pageSize  Jumlah maksimum item per halaman (default 200).
     * @param  int  $page  Nomor halaman aktif berbasis 1 (default 1).
     * @param  int  $totalCount  Total keseluruhan item lintas halaman (default 0).
     */
    public function __construct(
        public array $items = [],
        public int $pageSize = 200,
        public int $page = 1,
        public int $totalCount = 0,
    ) {}
}
