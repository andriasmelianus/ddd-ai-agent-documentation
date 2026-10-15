<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Bus\CommandBus;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * Job Laravel pembungkus eksekusi handler command secara asinkron.
 *
 * Ketika sebuah command didispatch dalam mode async oleh {@see SimpleCommandBus},
 * class ini akan diserialisasi lalu dijalankan oleh queue worker. Pada saat
 * `handle()` dijalankan, container me-resolve handler berdasarkan class-name
 * (`$this->handler`) dan memanggilnya dengan command yang tersimpan.
 *
 * Trait yang digunakan:
 *  - {@see Dispatchable} — menyediakan static `dispatch()`.
 *  - {@see InteractsWithQueue} — akses ke context queue (release, delete, dll).
 *  - {@see Queueable} — konfigurasi queue/connection/delay.
 *  - {@see SerializesModels} — otomatis (de)serialisasi Eloquent Model.
 */
class QueueHandler implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Strategi backoff antar percobaan (dalam detik) saat job gagal.
     *
     * @var int|array<int>
     */
    public int|array $backoff = [60, 120];

    /**
     * @param  string  $handler  Class-name handler yang akan diresolve container.
     * @param  CommandInterface  $command  Command yang akan diteruskan ke handler.
     */
    public function __construct(
        public string $handler,
        public CommandInterface $command,
    ) {}

    /**
     * Eksekusi handler dengan command yang tersimpan.
     *
     * Container diminta membuat instance handler (agar dependensinya
     * terinjeksi), kemudian handler dipanggil sebagai invokable.
     */
    public function handle(): void
    {
        /** @var CommandHandlerInterface $handler */
        $handler = app($this->handler);
        $handler->__invoke($this->command);
    }
}
