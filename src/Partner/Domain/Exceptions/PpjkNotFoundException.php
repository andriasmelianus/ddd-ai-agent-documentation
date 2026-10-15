<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Exceptions;

use DomainException;
use Src\Partner\Domain\ValueObjects\LegacyPpjkId;

final class PpjkNotFoundException extends DomainException
{
    public static function withId(LegacyPpjkId $id): self
    {
        return new self(sprintf('PPJK with ID %d was not found.', $id->value()));
    }
}
