<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Bus\QueryBus;

/**
 * Kontrak (interface) Query Bus untuk pola CQRS.
 *
 * Query Bus bertanggung jawab meneruskan objek {@see QueryInterface} ke
 * QueryHandler yang sesuai dan mengembalikan hasil pembacaan (read-model).
 * Berbeda dengan Command Bus yang tidak mengembalikan nilai (bersifat write),
 * Query Bus selalu menghasilkan output.
 *
 * Interface ini menyediakan empat variasi eksekusi query:
 *
 *  - {@see QueryBusInterface::query()}        — eksekusi query standar
 *  - {@see QueryBusInterface::queryOrFail()}  — eksekusi + throw jika hasil kosong/null
 *  - {@see QueryBusInterface::queryCached()}  — eksekusi + caching hasil
 *  - {@see QueryBusInterface::queryIndexed()} — eksekusi + rekonstruksi array ter-index
 *
 * Penggunaan generic `@template T` memungkinkan tipe hasil handler diinfer
 * berdasarkan `QueryInterface<T>` yang dilempar.
 */
interface QueryBusInterface
{
    /**
     * Menjalankan Query melalui handler yang sesuai dan mengembalikan hasilnya.
     *
     * @template T
     *
     * @param  QueryInterface<T>  $query  Query yang akan dijalankan.
     * @return T Hasil pemrosesan dari QueryHandler.
     *
     * @throws QueryBusException Bila handler melempar error domain terkait query.
     */
    public function query(QueryInterface $query);

    /**
     * Menjalankan Query lalu mengubah hasilnya menjadi array ter-index berdasarkan kolom tertentu.
     *
     * Berguna ketika hasil query berupa koleksi dan pemanggil butuh akses cepat
     * berdasarkan ID (mis. lookup map).
     *
     * @param  QueryInterface  $query  Query yang akan dijalankan.
     * @param  string  $index  Nama field/atribut yang dipakai sebagai kunci array.
     * @return array<mixed> Hasil query yang telah di-key oleh `$index`.
     *
     * @throws QueryBusException Bila handler melempar error domain terkait query.
     */
    public function queryIndexed(QueryInterface $query, string $index): array;

    /**
     * Menjalankan Query dengan caching hasil berdasarkan tanda tangan query.
     *
     * Kunci cache dihitung dari kombinasi nama kelas + serialisasi objek query,
     * sehingga query yang identik akan menghasilkan cache hit.
     *
     * @template T
     *
     * @param  QueryInterface<T>  $query  Query yang akan dijalankan.
     * @param  int|null  $ttl  TTL cache dalam detik. `null` = default implementasi (biasanya tanpa expire).
     * @return T Hasil query (dari cache atau eksekusi baru).
     *
     * @throws QueryBusException Bila handler melempar error domain terkait query.
     */
    public function queryCached(QueryInterface $query, ?int $ttl = null);

    /**
     * Menjalankan Query dan memastikan hasilnya tidak kosong; jika kosong akan melempar exception.
     *
     * Ekuivalen dengan pola `findOrFail()` pada Eloquent: berguna untuk endpoint
     * yang mengharuskan resource ada (mis. detail entitas berdasarkan ID).
     *
     * @template T
     *
     * @param  QueryInterface<T>  $query  Query yang akan dijalankan.
     * @return T Hasil query yang dijamin tidak `null` / tidak kosong.
     *
     * @throws QueryBusException Bila handler melempar error domain, atau bila hasil kosong/null
     *                           (biasanya melalui `ModelNotFoundException` yang dikategorikan sebagai QueryBusException).
     */
    public function queryOrFail(QueryInterface $query);
}
