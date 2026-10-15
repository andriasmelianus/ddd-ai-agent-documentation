<?php

declare(strict_types=1);

namespace Src\Company\Infrastructure\Persistence;

use Illuminate\Support\Facades\DB;
use Src\Company\Domain\ReadModels\CompanyRM;
use Src\Company\Domain\Repositories\CompanyQueryInterface;
use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Company\Domain\ValueObjects\Npwp;
use Src\Company\Domain\ValueObjects\TpsCode;
use Src\Company\Infrastructure\Mappers\CompanyMapper;

/**
 * Class CompanyQuery
 *
 * Infrastructure implementation of CompanyQueryInterface (CQRS Read Port).
 * Executes optimized read queries returning ReadModels directly without hydrating full aggregates.
 */
final readonly class CompanyQuery implements CompanyQueryInterface
{
    private const string TABLE = CompanyTable::TABLE_NAME;

    public function __construct(
        private CompanyMapper $mapper,
    ) {}

    public function findById(LegacyCompanyId $id): ?CompanyRM
    {
        $row = DB::table(self::TABLE)->where('id', $id->value())->first();

        if ($row === null) {
            return null;
        }

        return $this->mapper->toReadModel($row);
    }

    public function findByTpsCode(TpsCode $tpsCode): ?CompanyRM
    {
        $row = DB::table(self::TABLE)->where('tps_code', $tpsCode->value())->first();

        if ($row === null) {
            return null;
        }

        return $this->mapper->toReadModel($row);
    }

    public function findByNpwp(Npwp $npwp): ?CompanyRM
    {
        $row = DB::table(self::TABLE)
            ->where('tax_id_clean', $npwp->clean())
            ->orWhere('tax_id', $npwp->value())
            ->first();

        if ($row === null) {
            return null;
        }

        return $this->mapper->toReadModel($row);
    }

    public function exists(LegacyCompanyId $id): bool
    {
        return DB::table(self::TABLE)->where('id', $id->value())->exists();
    }

    /**
     * @return CompanyRM[]
     */
    public function findAllActive(): array
    {
        $rows = DB::table(self::TABLE)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $results = [];
        foreach ($rows as $row) {
            $results[] = $this->mapper->toReadModel($row);
        }

        return $results;
    }
}
