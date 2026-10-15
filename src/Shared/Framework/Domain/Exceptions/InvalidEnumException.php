<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\Exceptions;

use DomainException;

/**
 * Class InvalidEnumException
 *
 * Exception domain yang dilempar ketika sebuah nilai gagal dipetakan
 * (atau tidak valid) untuk suatu enum. Umumnya digunakan pada proses
 * konversi/validasi data dari luar (payload request, hasil query DB,
 * konfigurasi) ke Backed Enum internal.
 *
 * Mewarisi {@see \DomainException} — SPL exception yang secara semantik
 * memang ditujukan untuk kesalahan pada aturan domain (bukan runtime
 * error umum).
 *
 * Contoh pemakaian:
 * ```php
 * $status = StatusEnum::tryFrom($raw);
 * if ($status === null) {
 *     throw new InvalidEnumException("Nilai status '$raw' tidak dikenali.");
 * }
 * ```
 */
class InvalidEnumException extends DomainException
{
    /**
     * Membuat instance InvalidEnumException baru.
     *
     * @param  string  $message  Pesan opsional yang menjelaskan konteks
     *                           kegagalan (default: string kosong).
     */
    public function __construct(string $message = '')
    {
        parent::__construct($message);
    }
}
