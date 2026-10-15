<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\IO;

use Src\Shared\Framework\Domain\Services\LoggerInterface;

/**
 * Implementasi {@see LoggerInterface} yang menuliskan pesan log ke standard output (STDOUT).
 *
 * Kelas ini terutama dipakai pada konteks eksekusi CLI (Artisan command,
 * seeder, script populasi data, worker) di mana output langsung ke terminal
 * lebih berguna dibanding penulisan ke berkas log. Setiap pesan diformat
 * dengan awalan level (`[ERROR]`, `[INFO]`, `[WARNING]`, `[OK]`).
 *
 * Untuk logging di lingkungan produksi/aplikasi web, disarankan menggunakan
 * implementasi berbasis Monolog/Log Facade Laravel.
 */
class EchoLogger implements LoggerInterface
{
    /**
     * Mencetak pesan error dengan awalan `[ERROR]`.
     *
     * @param  string  $message  Pesan yang akan dicetak.
     */
    public function error(string $message): void
    {
        $this->output('ERROR', $message);
    }

    /**
     * Mencetak pesan informasi umum dengan awalan `[INFO]`.
     *
     * @param  string  $message  Pesan yang akan dicetak.
     */
    public function info(string $message): void
    {
        $this->output('INFO', $message);
    }

    /**
     * Mencetak pesan peringatan dengan awalan `[WARNING]`.
     *
     * @param  string  $message  Pesan yang akan dicetak.
     */
    public function warning(string $message): void
    {
        $this->output('WARNING', $message);
    }

    /**
     * Mencetak pesan sukses dengan awalan `[OK]`.
     *
     * @param  string  $message  Pesan yang akan dicetak.
     */
    public function success(string $message): void
    {
        $this->output('OK', $message);
    }

    /**
     * Menuliskan pesan ke STDOUT dalam format ` [TYPE] message\n`.
     *
     * @param  string  $type  Label level log (ERROR/INFO/WARNING/OK).
     * @param  string  $message  Isi pesan.
     */
    private function output(string $type, string $message): void
    {
        echo ' ['.$type.'] '.$message."\n";
    }
}
