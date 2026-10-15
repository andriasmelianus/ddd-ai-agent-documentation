<?php

declare(strict_types=1);

namespace Src\Partner\Domain\Exceptions;

use DomainException;
use Src\Partner\Domain\ValueObjects\LegacyConsigneeId;

final class ConsigneeNotFoundException extends DomainException
{
    public static function withId(LegacyConsigneeId $id): self
    {
        return new self(sprintf('Consignee with ID %d was not found.', $id->value()));
    }

    public static function withTaxId(string $taxId): self
    {
        return new self(sprintf('Consignee with Tax ID (NPWP) "%s" was not found.', $taxId));
    }
}
