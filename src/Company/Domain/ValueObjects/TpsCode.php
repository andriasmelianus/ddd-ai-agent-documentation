<?php

declare(strict_types=1);

namespace Src\Company\Domain\ValueObjects;

use Src\Shared\Common\ValueObjects\BaseCode;

/**
 * Class TpsCode
 *
 * Value Object representing Customs TPS (Tempat Penimbunan Sementara) Code.
 */
readonly class TpsCode extends BaseCode
{
    public static function fromStringOrNull(?string $value): ?self
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return new self($value);
    }
}
