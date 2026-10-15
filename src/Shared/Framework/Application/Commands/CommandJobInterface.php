<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Application\Commands;

/**
 * Kontrak tambahan untuk Command yang perlu dieksekusi secara asynchronous
 * melalui mekanisme queue Laravel.
 *
 * Command yang meng-implement interface ini akan diperlakukan berbeda oleh
 * {@see \Src\Shared\Framework\Infrastructure\Bus\CommandBus\CommandBusInterface}:
 * alih-alih dieksekusi langsung (in-process), command tersebut dapat
 * di-dispatch ke queue tertentu ({@see getQueue()}) dengan penundaan opsional
 * ({@see getDelay()}).
 */
interface CommandJobInterface
{
    /**
     * Mengembalikan nama queue tujuan dispatch command.
     *
     * @return QueueEnum Nama queue (misal default, marketing, dsb).
     */
    public function getQueue(): QueueEnum;

    /**
     * Menentukan apakah command harus benar-benar dijalankan melalui queue.
     *
     * Dengan mengembalikan false, command dapat dieksekusi sinkron meskipun
     * meng-implement interface ini (berguna untuk kondisi feature-flag / env).
     *
     * @return bool True bila harus di-queue, false bila dieksekusi sinkron.
     */
    public function shouldQueue(): bool;

    /**
     * Mengembalikan besaran delay (dalam detik) sebelum job dieksekusi worker.
     *
     * @return int Jumlah detik penundaan; 0 berarti tanpa penundaan.
     */
    public function getDelay(): int;
}
