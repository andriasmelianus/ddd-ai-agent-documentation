<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Bus\EventBus;

use Src\Shared\Framework\Domain\Events\DomainEvent;

/**
 * Implementasi sederhana {@see EventBusInterface} yang mendispatch Domain Event
 * melalui sistem event bawaan Laravel.
 *
 * Setiap event yang diterima akan diteruskan ke fungsi helper global `event()`
 * milik Laravel sehingga seluruh listener yang telah didaftarkan pada
 * `EventServiceProvider` (atau discovery otomatis) akan dieksekusi.
 *
 * Kelas ini bersifat `final` karena bertindak sebagai adapter tipis terhadap
 * infrastruktur Laravel dan tidak dimaksudkan untuk diperluas melalui pewarisan.
 * Bila diperlukan strategi publikasi berbeda (mis. antrean, RabbitMQ, Kafka),
 * disarankan membuat implementasi baru dari {@see EventBusInterface}.
 */
final class SimpleEventBus implements EventBusInterface
{
    /**
     * Mempublikasikan sekumpulan Domain Event ke sistem event Laravel.
     *
     * Melakukan iterasi terhadap array `$events` lalu memanggil `event($event)`
     * untuk setiap elemen. Urutan publikasi mengikuti urutan pada array input.
     *
     * @param  DomainEvent[]  $events  Daftar Domain Event yang akan dipublikasikan.
     */
    public function publishEvents(array $events): void
    {
        foreach ($events as $event) {
            event($event);
        }
    }
}
