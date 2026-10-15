<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\Services;

/**
 * Interface LoggerInterface
 *
 * Kontrak layanan logging tingkat **domain** untuk framework internal.
 * Interface ini sengaja dibuat minimalis dan bebas dari
 * ketergantungan pada library eksternal (mis. PSR-3 / Monolog / Laravel
 * Log facade) agar layer domain tetap bersih dan mudah diuji.
 *
 * Implementasi konkret (mis.
 * {@see \Src\Shared\Framework\Infrastructure\IO\EchoLogger} atau
 * {@see \Src\Shared\Framework\Infrastructure\Logging\LoggerHelper})
 * yang bertanggung jawab menerjemahkan panggilan ini menjadi output
 * nyata — apakah ke STDOUT, file, database, atau layanan monitoring
 * eksternal.
 *
 * Setiap method menerima **string tunggal** — jika perlu menyisipkan
 * konteks, disarankan format string tersebut sudah di-compose oleh
 * caller (mis. dengan `sprintf()` atau serialisasi JSON) sebelum
 * dilempar ke logger.
 */
interface LoggerInterface
{
    /**
     * Mencatat pesan sebagai **error** — kejadian yang mengindikasikan
     * kegagalan dan biasanya membutuhkan tindakan (mis. exception yang
     * tidak tertangani, kegagalan integrasi, dsb).
     *
     * @param  string  $message  Pesan error yang akan dicatat.
     */
    public function error(string $message): void;

    /**
     * Mencatat pesan **informational** — informasi umum tentang jalannya
     * proses (mis. "Job X mulai diproses", "Batch import selesai"), yang
     * berguna untuk audit atau trace tanpa mengindikasikan masalah.
     *
     * @param  string  $message  Pesan informasi yang akan dicatat.
     */
    public function info(string $message): void;

    /**
     * Mencatat pesan **warning** — peringatan bahwa terjadi sesuatu yang
     * tidak biasa, namun proses masih dapat berjalan (mis. konfigurasi
     * usang, retry, data mendekati batas kuota).
     *
     * @param  string  $message  Pesan peringatan yang akan dicatat.
     */
    public function warning(string $message): void;

    /**
     * Mencatat pesan **success** — konfirmasi bahwa suatu operasi telah
     * selesai dengan sukses. Level ini bersifat semantik-domain (bukan
     * level PSR-3 standar) dan berguna khususnya di skenario batch/CLI
     * agar tampak jelas step yang sudah tuntas.
     *
     * @param  string  $message  Pesan sukses yang akan dicatat.
     */
    public function success(string $message): void;
}
