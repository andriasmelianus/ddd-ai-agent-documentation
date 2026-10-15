<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Exceptions;

use DomainException;
use Src\Ceisa\Domain\ValueObjects\KodeGudang;
use Src\Shared\Framework\Domain\Exceptions\VisibleExceptionInterface;

final class AuthLoginResultNotFoundException extends DomainException implements VisibleExceptionInterface
{
    public static function withKodeGudang(KodeGudang $kodeGudang): self
    {
        return new self(sprintf(
            'Auth login result for warehouse code "%s" was not found.',
            $kodeGudang->value(),
        ));
    }

    public static function withWarehouseCode(KodeGudang $warehouseCode): self
    {
        return self::withKodeGudang($warehouseCode);
    }
}

