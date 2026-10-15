<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\Exceptions;

use DomainException;
use Src\Shared\Framework\Helpers\MixedHelper;

/**
 * Class InvalidIdentifierException
 *
 * Exception domain yang dilempar ketika sebuah nilai gagal dijadikan
 * Identifier (misalnya {@see \Src\Shared\Framework\Domain\ValueObjects\Ulid},
 * {@see \Src\Shared\Framework\Domain\ValueObjects\Uuid}, atau
 * {@see \Src\Shared\Framework\Domain\ValueObjects\StringIdentifier})
 * karena tidak memenuhi aturan format/panjang/karakter yang ditentukan.
 *
 * Pesan exception dibentuk secara otomatis dari nama kelas Identifier
 * yang gagal dan representasi string dari nilai yang menyebabkan
 * kegagalan — dikonversi secara aman menggunakan
 * {@see MixedHelper::getStringOrNull()} agar aman untuk semua tipe input.
 *
 * Contoh pemakaian:
 * ```php
 * if (! Uuid::isValid($raw)) {
 *     throw new InvalidIdentifierException(Uuid::class, $raw);
 * }
 * ```
 */
class InvalidIdentifierException extends DomainException
{
    /**
     * Membuat instance InvalidIdentifierException baru.
     *
     * @param  string  $class  Fully-qualified class name Identifier yang
     *                         gagal (mis. `Ulid::class`).
     * @param  mixed  $value  Nilai mentah yang menyebabkan kegagalan. Nilai
     *                        apapun boleh — akan dikonversi ke string secara
     *                        aman lewat {@see MixedHelper::getStringOrNull()}.
     */
    public function __construct(string $class, $value)
    {
        parent::__construct($class.' '.MixedHelper::getStringOrNull($value));
    }
}
