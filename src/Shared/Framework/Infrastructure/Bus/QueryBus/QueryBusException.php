<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Bus\QueryBus;

use DomainException;

/**
 * Exception dasar (abstract) untuk seluruh error yang berasal dari Query Bus.
 *
 * Kelas ini merupakan turunan {@see DomainException} sehingga secara semantik
 * dikategorikan sebagai kesalahan domain (bukan kesalahan runtime infrastruktur).
 * Digunakan sebagai parent class untuk exception spesifik yang dilempar oleh
 * QueryBus atau QueryHandler (mis. `QueryValidationException`, `QueryTimeoutException`, dsb).
 *
 * Karena bersifat `abstract`, kelas ini tidak boleh di-instansiasi langsung —
 * konsumen harus membuat subclass konkret dengan pesan/context yang relevan.
 */
abstract class QueryBusException extends DomainException {}
