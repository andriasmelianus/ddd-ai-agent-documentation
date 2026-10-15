<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Exceptions;

use DomainException;
use Src\Ceisa\Domain\ValueObjects\KodeGudang;
use Src\Shared\Framework\Domain\Exceptions\VisibleExceptionInterface;

final class AuthCredentialsNotFoundException extends DomainException implements VisibleExceptionInterface
{
    public static function withKodeGudang(KodeGudang $kodeGudang): self
    {
        return new self(sprintf(
            'Auth credentials for warehouse code "%s" was not found in companies table.',
            $kodeGudang->value(),
        ));
    }

    public static function withWarehouseCode(KodeGudang $warehouseCode): self
    {
        return self::withKodeGudang($warehouseCode);
    }

    public static function missingCredentials(KodeGudang $kodeGudang): self
    {
        return new self(sprintf(
            'CEISA 4.0 username or password for warehouse code "%s" is empty or not configured.',
            $kodeGudang->value(),
        ));
    }
}

