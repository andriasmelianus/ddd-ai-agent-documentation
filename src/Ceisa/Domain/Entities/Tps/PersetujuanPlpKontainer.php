<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Entities\Tps;

use DateTimeImmutable;
use DateTimeInterface;
use Src\Ceisa\Domain\Enums\FlagPersetujuan;
use Src\Ceisa\Domain\Enums\JenisKontainer;
use Src\Ceisa\Domain\Enums\UkuranKontainer;
use Src\Ceisa\Domain\Exceptions\Tps\PersetujuanPlpException;
use Src\Ceisa\Domain\ValueObjects\BlAwbIdentifier;
use Src\Ceisa\Domain\ValueObjects\NomorKontainer;
use Src\Ceisa\Domain\ValueObjects\NomorPosBc11;
use Src\Ceisa\Domain\ValueObjects\Tps\PersetujuanPlpKontainerId;
use Src\Shared\Framework\Domain\Entities\BaseEntity;
use Src\Shared\Framework\Domain\ValueObjects\Timestamp;

/**
 * Class PersetujuanPlpKontainer
 *
 * Child Entity representing a container item (kontainer) within a Persetujuan PLP aggregate.
 */
class PersetujuanPlpKontainer extends BaseEntity
{
    private function __construct(
        public readonly PersetujuanPlpKontainerId $id,
        public private(set) NomorKontainer $nomorKontainer,
        public private(set) UkuranKontainer|string $ukuranKontainer,
        public private(set) JenisKontainer|string $jenisKontainer,
        public private(set) NomorPosBc11 $nomorPosBc11,
        public private(set) BlAwbIdentifier $blAwbIdentifier,
        public private(set) string $consignee,
        public private(set) FlagPersetujuan $flagSetuju,
        public private(set) ?int $idTdTpsPlp = null,
    ) {}

    public static function create(
        NomorKontainer|string $nomorKontainer,
        UkuranKontainer|string $ukuranKontainer,
        JenisKontainer|string $jenisKontainer,
        NomorPosBc11|string $nomorPosBc11,
        BlAwbIdentifier|string $nomorBlAwb,
        DateTimeImmutable|DateTimeInterface|string|null $tanggalBlAwb = null,
        string $consignee = '',
        FlagPersetujuan|string $flagSetuju = FlagPersetujuan::SETUJU,
        PersetujuanPlpKontainerId|string|null $id = null,
        ?int $idTdTpsPlp = null,
    ): self {
        $trimmedConsignee = trim($consignee);
        if ($trimmedConsignee === '') {
            throw PersetujuanPlpException::emptyConsignee();
        }

        $containerVO = $nomorKontainer instanceof NomorKontainer
            ? $nomorKontainer
            : NomorKontainer::fromUnverified((string) $nomorKontainer, strictCategory: false);

        $resolvedUkuran = $ukuranKontainer instanceof UkuranKontainer
            ? $ukuranKontainer
            : (UkuranKontainer::tryFrom(trim((string) $ukuranKontainer)) ?? trim((string) $ukuranKontainer));

        $resolvedJenis = $jenisKontainer instanceof JenisKontainer
            ? $jenisKontainer
            : (JenisKontainer::tryFrom(trim((string) $jenisKontainer)) ?? trim((string) $jenisKontainer));

        $posBc11VO = $nomorPosBc11 instanceof NomorPosBc11
            ? $nomorPosBc11
            : NomorPosBc11::fromString($nomorPosBc11);

        $blAwbVO = $nomorBlAwb instanceof BlAwbIdentifier
            ? $nomorBlAwb
            : BlAwbIdentifier::create((string) $nomorBlAwb, $tanggalBlAwb ?? new DateTimeImmutable('today'));

        $resolvedFlag = $flagSetuju instanceof FlagPersetujuan
            ? $flagSetuju
            : FlagPersetujuan::fromValue($flagSetuju);

        $entityId = $id instanceof PersetujuanPlpKontainerId
            ? $id
            : ($id !== null ? PersetujuanPlpKontainerId::fromString((string) $id) : PersetujuanPlpKontainerId::random());

        $entity = new self(
            id: $entityId,
            nomorKontainer: $containerVO,
            ukuranKontainer: $resolvedUkuran,
            jenisKontainer: $resolvedJenis,
            nomorPosBc11: $posBc11VO,
            blAwbIdentifier: $blAwbVO,
            consignee: $trimmedConsignee,
            flagSetuju: $resolvedFlag,
            idTdTpsPlp: $idTdTpsPlp,
        );

        $entity->setIsNew(true);

        return $entity;
    }

    public static function reconstitute(
        PersetujuanPlpKontainerId|string $id,
        NomorKontainer $nomorKontainer,
        UkuranKontainer|string $ukuranKontainer,
        JenisKontainer|string $jenisKontainer,
        NomorPosBc11 $nomorPosBc11,
        BlAwbIdentifier $blAwbIdentifier,
        string $consignee,
        FlagPersetujuan $flagSetuju,
        ?Timestamp $timestamps = null,
        ?int $idTdTpsPlp = null,
    ): self {
        $resolvedId = $id instanceof PersetujuanPlpKontainerId
            ? $id
            : PersetujuanPlpKontainerId::fromString((string) $id);

        $entity = new self(
            id: $resolvedId,
            nomorKontainer: $nomorKontainer,
            ukuranKontainer: $ukuranKontainer,
            jenisKontainer: $jenisKontainer,
            nomorPosBc11: $nomorPosBc11,
            blAwbIdentifier: $blAwbIdentifier,
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

    public function id(): PersetujuanPlpKontainerId
    {
        return $this->id;
    }

    public function idTdTpsPlp(): ?int
    {
        return $this->idTdTpsPlp;
    }

    public function nomorKontainer(): NomorKontainer
    {
        return $this->nomorKontainer;
    }

    public function ukuranKontainer(): UkuranKontainer|string
    {
        return $this->ukuranKontainer;
    }

    public function jenisKontainer(): JenisKontainer|string
    {
        return $this->jenisKontainer;
    }

    public function nomorPosBc11(): NomorPosBc11
    {
        return $this->nomorPosBc11;
    }

    public function blAwbIdentifier(): BlAwbIdentifier
    {
        return $this->blAwbIdentifier;
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
}
