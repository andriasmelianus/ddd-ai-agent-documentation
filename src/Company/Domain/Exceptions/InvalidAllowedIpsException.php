<?php

declare(strict_types=1);

namespace Src\Company\Domain\Exceptions;

use DomainException;

final class InvalidAllowedIpsException extends DomainException
{
    public static function invalidIpOrCidr(string $ip): self
    {
        return new self(sprintf(
            'Invalid IP address or CIDR notation: "%s".',
            $ip,
        ));
    }
}
