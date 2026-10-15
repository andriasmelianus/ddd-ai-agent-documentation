<?php

declare(strict_types=1);

namespace Src\Company\Domain\Exceptions;

use DomainException;
use Src\Company\Domain\ValueObjects\LegacyCompanyId;

final class CompanyNotFoundException extends DomainException
{
    public static function withId(LegacyCompanyId $id): self
    {
        return new self(sprintf('Company with ID %d was not found.', $id->value()));
    }
}
