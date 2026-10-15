<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Queue;

use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Redis;

/**
 * Event listener untuk mencatat jumlah job antrean yang **berhasil diproses**.
 *
 * Kelas ini didesain untuk didaftarkan sebagai listener terhadap event
 * {@see JobProcessed} Laravel (biasanya via `Queue::after(...)` atau
 * `EventServiceProvider`). Setiap kali sebuah job selesai dieksekusi tanpa
 * error, counter Redis harian per-queue akan dinaikkan sebesar 1.
 *
 * ### Format kunci Redis
 * `queue_jobs_completed:{queueName}:today:{YYYY-MM-DD}`
 *
 * Metrik ini berguna untuk dashboard monitoring queue (mis. jumlah job sukses
 * per hari per queue), audit throughput, atau alerting sederhana.
 */
class TrackJobCompletion
{
    /**
     * Membuat instance event listener.
     *
     * Tidak memerlukan dependensi eksternal; Laravel akan mengelola
     * pembuatan instance ini via Service Container.
     */
    public function __construct()
    {
        //
    }

    /**
     * Menangani event {@see JobProcessed}.
     *
     * Mengambil nama queue dari `$event->job->getQueue()`, membentuk kunci Redis
     * bertanggal (hari ini, format `Y-m-d`), lalu memanggil `INCR` pada koneksi
     * Redis default. Kunci akan otomatis ter-buat dengan nilai 1 bila belum ada.
     *
     * @param  JobProcessed  $event  Event Laravel yang dilempar setelah job sukses.
     */
    public function handle(JobProcessed $event): void
    {

        $queueName = $event->job->getQueue();
        $today = date('Y-m-d');
        $connection = Redis::connection();
        $connection->incr("queue_jobs_completed:{$queueName}:today:{$today}");
    }
}
