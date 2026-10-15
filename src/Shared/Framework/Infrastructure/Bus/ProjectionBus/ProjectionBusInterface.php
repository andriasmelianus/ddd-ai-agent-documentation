<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Bus\ProjectionBus;

use Src\Shared\Framework\Application\Projections\ProjectionInterface;

/**
 * Kontrak (interface) Projection Bus.
 *
 * Projection Bus bertugas meneruskan sebuah objek {@see ProjectionInterface}
 * ke ProjectionHandler yang sesuai. Konsep "projection" di sini merupakan
 * bagian dari pola CQRS/Event-Sourcing, di mana data domain diproyeksikan
 * menjadi bentuk representasi baca (read-model) yang dioptimalkan untuk
 * kebutuhan query tertentu.
 *
 * Berbeda dengan Command Bus (tanpa nilai balik) dan Query Bus (mengembalikan
 * read-model), Projection Bus dapat mengembalikan `mixed` — hasilnya bergantung
 * pada handler yang menangani projection tersebut (mis. entitas hasil sinkronisasi,
 * ID record baru, atau `void`).
 */
interface ProjectionBusInterface
{
    /**
     * Memproyeksikan objek Projection ke handler yang sesuai.
     *
     * Implementasi akan me-resolve handler berdasarkan konvensi penamaan
     * (mis. `XxxProjection` → `XxxHandler`) atau routing eksplisit, lalu
     * menjalankan handler tersebut terhadap projection yang diberikan.
     *
     * @param  ProjectionInterface  $projection  Objek projection yang akan diproses.
     * @return mixed Hasil eksekusi handler (dapat berupa entitas, ID, atau nilai lain sesuai kebutuhan).
     */
    public function project(ProjectionInterface $projection): mixed;
}
