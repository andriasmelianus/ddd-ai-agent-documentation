<?php

declare(strict_types=1);

namespace Src\Company\Infrastructure\Persistence;

/**
 * Class CompanyTable
 *
 * Database table constants and schema definition for the Company persistence layer.
 * Enforces Rule #6 in 04-infrastructure-layer.md to avoid table name string duplication.
 */
final class CompanyTable
{
    public const string TABLE_NAME = 'companies';
}
