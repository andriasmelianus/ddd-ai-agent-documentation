<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Application\Commands;

/**
 * Daftar nama queue yang tersedia untuk mekanisme
 * asynchronous command / job pada aplikasi.
 *
 * Nilai enum ini digunakan oleh {@see CommandJobInterface::getQueue()} untuk
 * menentukan queue tujuan saat command di-dispatch ke worker.
 */
enum QueueEnum: string
{
    /** Queue standar untuk pekerjaan umum yang tidak memerlukan segregasi. */
    case default = 'default';

    /** Queue khusus untuk pekerjaan bertema marketing / promosi. */
    case marketing = 'marketing';
}
