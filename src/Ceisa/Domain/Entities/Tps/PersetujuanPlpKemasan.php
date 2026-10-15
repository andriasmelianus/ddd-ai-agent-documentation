<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Entities\Tps;

use DateTimeImmutable;
use DateTimeInterface;
use Src\Ceisa\Domain\Enums\FlagPersetujuan;
use Src\Ceisa\Domain\Enums\JenisKemasan;
use Src\Ceisa\Domain\Exceptions\Tps\PersetujuanPlpException;
use Src\Ceisa\Domain\ValueObjects\BlAwbIdentifier;
use Src\Ceisa\Domain\ValueObjects\NomorPosBc11;
use Src\Ceisa\Domain\ValueObjects\Tps\PersetujuanPlpKemasanId;
use Src\Shared\Framework\Domain\Entities\BaseEntity;
use Src\Shared\Framework\Domain\ValueObjects\Timestamp;

/**
 * Class PersetujuanPlpKemasan
 *
 * Child Entity representing a package item (kemasan) within a Persetujuan PLP aggregate.
 */
class PersetujuanPlpKemasan extends BaseEntity
{
    private function __construct(
        public readonly PersetujuanPlpKemasanId $id,
        public private(set) BlAwbIdentifier $blAwbIdentifier,
        public private(set) NomorPosBc11 $nomorPosBc11,
        public private(set) float $jumlahKemasan,
        public private(set) JenisKemasan|string $jenisKemasan,
        public private(set) string $consignee,
        public private(set) FlagPersetujuan $flagSetuju,
        public private(set) ?int $idTdTpsPlp = null,
    ) {}

    public static function create(
        NomorPosBc11|string $nomorPosBc11,
        float|int|string $jumlahKemasan,
        JenisKemasan|string $jenisKemasan,
        BlAwbIdentifier|string $nomorBlAwb,
        DateTimeImmutable|DateTimeInterface|string|null $tanggalBlAwb = null,
        string $consignee = '',
        FlagPersetujuan|string $flagSetuju = FlagPersetujuan::SETUJU,
        PersetujuanPlpKemasanId|string|null $id = null,
        ?int $idTdTpsPlp = null,
    ): self {
        $trimmedConsignee = trim($consignee);
        if ($trimmedConsignee === '') {
            throw PersetujuanPlpException::emptyConsignee();
        }

        $resolvedJumlahKemasan = (float) $jumlahKemasan;
        if ($resolvedJumlahKemasan < 0) {
            throw PersetujuanPlpException::negativeJumlahKemasan($resolvedJumlahKemasan);
        }

        $posBc11VO = $nomorPosBc11 instanceof NomorPosBc11
            ? $nomorPosBc11
            : NomorPosBc11::fromString($nomorPosBc11);

        $blAwbVO = $nomorBlAwb instanceof BlAwbIdentifier
            ? $nomorBlAwb
            : BlAwbIdentifier::create((string) $nomorBlAwb, $tanggalBlAwb ?? new DateTimeImmutable('today'));

        $resolvedJenisKemasan = $jenisKemasan instanceof JenisKemasan
            ? $jenisKemasan
            : (JenisKemasan::tryFrom(trim((string) $jenisKemasan)) ?? trim((string) $jenisKemasan));

        $resolvedFlag = $flagSetuju instanceof FlagPersetujuan
            ? $flagSetuju
            : FlagPersetujuan::fromValue($flagSetuju);

        $entityId = $id instanceof PersetujuanPlpKemasanId
            ? $id
            : ($id !== null ? PersetujuanPlpKemasanId::fromString((string) $id) : PersetujuanPlpKemasanId::random());

        $entity = new self(
            id: $entityId,
            blAwbIdentifier: $blAwbVO,
            nomorPosBc11: $posBc11VO,
            jumlahKemasan: $resolvedJumlahKemasan,
            jenisKemasan: $resolvedJenisKemasan,
            consignee: $trimmedConsignee,
            flagSetuju: $resolvedFlag,
            idTdTpsPlp: $idTdTpsPlp,
        );

        $entity->setIsNew(true);

        return $entity;
    }

    public static function reconstitute(
        PersetujuanPlpKemasanId|string $id,
        BlAwbIdentifier $blAwbIdentifier,
        NomorPosBc11 $nomorPosBc11,
        float|int $jumlahKemasan,
        JenisKemasan|string $jenisKemasan,
        string $consignee,
        FlagPersetujuan $flagSetuju,
        ?Timestamp $timestamps = null,
        ?int $idTdTpsPlp = null,
    ): self {
        $resolvedId = $id instanceof PersetujuanPlpKemasanId
            ? $id
            : PersetujuanPlpKemasanId::fromString((string) $id);

        $entity = new self(
            id: $resolvedId,
            blAwbIdentifier: $blAwbIdentifier,
            nomorPosBc11: $nomorPosBc11,
            jumlahKemasan: (float) $jumlahKemasan,
            jenisKemasan: $jenisKemasan,
            consignee: $consignee,
            flagSetuju: $flagSetuju,
            idTdTpsPlp: $idTdTpsPlp,
        );

        $entity->setIsNew(false);
        if ($timestamps !== null) {
            $entity->timestamps = $timestamps;
        }

        $entity->makeCheckPoint();

        return $entity;
    }

    public function setujui(): void
    {
        $this->flagSetuju = FlagPersetujuan::SETUJU;
    }

    public function tolak(): void
    {
        $this->flagSetuju = FlagPersetujuan::TOLAK;
    }

    public function id(): PersetujuanPlpKemasanId
    {
        return $this->id;
    }

    public function idTdTpsPlp(): ?int
    {
        return $this->idTdTpsPlp;
    }

    public function blAwbIdentifier(): BlAwbIdentifier
    {
        return $this->blAwbIdentifier;
    }

    public function nomorPosBc11(): NomorPosBc11
    {
        return $this->nomorPosBc11;
    }

    public function jumlahKemasan(): float
    {
        return $this->jumlahKemasan;
    }

    public function jenisKemasan(): JenisKemasan|string
    {
        return $this->jenisKemasan;
    }

    public function consignee(): string
    {
        return $this->consignee;
    }

    public function flagSetuju(): FlagPersetujuan
    {
        return $this->flagSetuju;
    }

    public function isSetuju(): bool
    {
        return $this->flagSetuju === FlagPersetujuan::SETUJU;
    }

    public function isTolak(): bool
    {
        return $this->flagSetuju === FlagPersetujuan::TOLAK;
    }

    public function compositeKey(): string
    {
        return sprintf('%s:%s', $this->blAwbIdentifier->nomorBlAwb(), $this->nomorPosBc11->value);
    }
}
