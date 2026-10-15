<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Logging;

use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Helper logging tipis yang menggunakan Log facade bawaan Laravel.
 *
 * Kelas ini menyediakan cara singkat untuk mencatat exception ke channel
 * log default aplikasi tanpa perlu memasang boilerplate kontekstual yang sama
 * berulang kali. Umumnya dipanggil dari `try/catch` di lapisan Application
 * atau Infrastructure untuk mencatat kegagalan proses.
 *
 * Kelas ini bersifat `final` dan berisi metode statik — tidak dimaksudkan
 * untuk di-instansiasi maupun diwariskan.
 */
final class LoggerHelper
{
    /**
     * Mencatat sebuah exception ke log dengan level `error` beserta konteks lengkapnya.
     *
     * Konteks yang dicatat mencakup:
     *  - `exception` : objek exception aslinya (agar handler log dapat mem-format struktur)
     *  - `trace`     : stack trace dalam bentuk string ({@see Throwable::getTraceAsString()})
     *
     * Pesan utama diambil dari {@see Throwable::getMessage()}.
     *
     * @param  Throwable  $exception  Exception yang akan dicatat.
     */
    public static function logException(Throwable $exception): void
    {
        Log::error($exception->getMessage(), [
            'exception' => $exception,
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
