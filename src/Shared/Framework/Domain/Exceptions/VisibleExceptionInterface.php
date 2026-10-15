<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\Exceptions;

/**
 * Interface VisibleExceptionInterface
 *
 * Marker interface (tidak memiliki method) yang menandai sebuah exception
 * sebagai **"aman untuk ditampilkan ke user"**. Exception yang
 * mengimplementasikan interface ini boleh diteruskan pesan aslinya ke
 * response HTTP / UI tanpa disamarkan menjadi generic error.
 *
 * Global exception handler biasanya melakukan pengecekan `instanceof
 * VisibleExceptionInterface` untuk memutuskan apakah `getMessage()` bisa
 * ditampilkan langsung, atau harus di-mask menjadi pesan generik seperti
 * `"Internal Server Error"` demi keamanan (agar detail internal tidak bocor).
 *
 * Contoh implementasi:
 * ```php
 * final class DuplicateEmailException extends DomainException
 *     implements VisibleExceptionInterface
 * {
 *     public function __construct(string $email)
 *     {
 *         parent::__construct("Email '$email' sudah terdaftar.");
 *     }
 * }
 * ```
 */
interface VisibleExceptionInterface {}
