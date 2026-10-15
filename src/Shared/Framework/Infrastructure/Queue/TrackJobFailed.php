<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Queue;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Redis;

/**
 * Event listener untuk mencatat jumlah job antrean yang **gagal diproses**.
 *
 * Kelas ini merupakan pasangan dari {@see TrackJobCompletion} untuk sisi
 * kegagalan. Didaftarkan sebagai listener terhadap event {@see JobFailed}
 * Laravel (biasanya via `Queue::failing(...)` atau `EventServiceProvider`).
 * Setiap kali sebuah job melempar exception hingga menyerah (habis retry),
 * counter Redis harian per-queue akan dinaikkan sebesar 1.
 *
 * ### Format kunci Redis
 * `queue_jobs_failed:{queueName}:today:{YYYY-MM-DD}`
 *
 * Kombinasi metrik `completed` + `failed` dapat dipakai untuk menghitung
 * success rate queue dan memicu alert bila rasio kegagalan melewati ambang.
 */
class TrackJobFailed
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
     * Menangani event {@see JobFailed}.
     *
     * Mengambil nama queue dari `$event->job->getQueue()`, membentuk kunci Redis
     * bertanggal (hari ini, format `Y-m-d`), lalu memanggil `INCR` pada koneksi
     * Redis default.
     *
     * @param  JobFailed  $event  Event Laravel yang dilempar ketika job gagal secara final.
     */
    public function handle(JobFailed $event): void
    {

        $queueName = $event->job->getQueue();
        $today = date('Y-m-d');
        $connection = Redis::connection();
        $connection->incr("queue_jobs_failed:{$queueName}:today:{$today}");
    }
}
