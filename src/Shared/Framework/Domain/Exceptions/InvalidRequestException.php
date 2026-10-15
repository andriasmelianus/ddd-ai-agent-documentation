<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\Exceptions;

use DomainException;

/**
 * Class InvalidRequestException
 *
 * Exception domain yang merepresentasikan request tidak valid dari sisi
 * bisnis (mis. kombinasi field tidak diperbolehkan, aturan validasi
 * business rule gagal, dsb).
 *
 * Kelas ini di-set dengan HTTP status code **422 Unprocessable Entity**
 * melalui properti {@see self::$code}, sehingga pada layer HTTP layer dapat
 * langsung meneruskan `getCode()` sebagai status response.
 *
 * Bersifat `final` karena bertindak sebagai leaf exception yang siap
 * pakai — bila diperlukan konteks tambahan, disarankan membuat exception
 * baru yang lebih spesifik.
 *
 * Contoh pemakaian:
 * ```php
 * throw InvalidRequestException::fromMessage('Tanggal mulai harus <= tanggal akhir.');
 * ```
 */
final class InvalidRequestException extends DomainException
{
    /**
     * Kode default exception ini = 422 (HTTP Unprocessable Entity).
     *
     * @var int
     */
    protected $code = 422;

    /**
     * Named constructor yang membuat instance dari pesan string.
     *
     * Disediakan untuk gaya pemakaian yang lebih ekspresif
     * (`InvalidRequestException::fromMessage(...)`) dibanding
     * `new InvalidRequestException(...)`.
     *
     * @param  string  $message  Pesan yang menjelaskan alasan request ditolak.
     * @return self Instance baru exception dengan pesan yang diberikan.
     */
    public static function fromMessage(string $message): self
    {
        return new self($message);
    }
}
