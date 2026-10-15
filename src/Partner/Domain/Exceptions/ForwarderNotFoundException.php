<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Exceptions;

use DomainException;
use Src\Partner\Domain\ValueObjects\LegacyForwarderId;

final class ForwarderNotFoundException extends DomainException
{
    public static function withId(LegacyForwarderId $id): self
    {
        return new self(sprintf('Forwarder with ID %d was not found.', $id->value()));
    }
}
