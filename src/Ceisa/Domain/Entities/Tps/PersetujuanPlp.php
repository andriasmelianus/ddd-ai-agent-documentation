<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Entities\Tps;

use DateTimeImmutable;
use Src\Ceisa\Domain\Events\Tps\PersetujuanPlpCreated;
use Src\Ceisa\Domain\Exceptions\Tps\PersetujuanPlpException;
use Src\Ceisa\Domain\ValueObjects\Bc11Identifier;
use Src\Ceisa\Domain\ValueObjects\BlAwbIdentifier;
use Src\Ceisa\Domain\ValueObjects\KodeGudang;
use Src\Ceisa\Domain\ValueObjects\KodeKantor;
use Src\Ceisa\Domain\ValueObjects\KodeTps;
use Src\Ceisa\Domain\ValueObjects\NomorKontainer;
use Src\Ceisa\Domain\ValueObjects\NomorPosBc11;
use Src\Ceisa\Domain\ValueObjects\SuratIdentifier;
use Src\Ceisa\Domain\ValueObjects\Tps\PersetujuanPlpId;
use Src\Ceisa\Domain\ValueObjects\Tps\PersetujuanPlpIdentifier;
use Src\Shared\Framework\Domain\Entities\BaseAggregateRoot;
use Src\Shared\Framework\Domain\ValueObjects\Timestamp;

/**
 * Class PersetujuanPlp
 *
 * Aggregate Root representing Customs Approval for Relocation of Storage Location
 * (Persetujuan Pindah Lokasi Penimbunan - PLP).
 *
 * Invariants:
 * - Origin location (TPS and warehouse) cannot be identical to destination location.
 * - Containers are unique by container number within the aggregate.
 * - Packages (kemasan) are unique by B/L or AWB and Pos BC 1.1 composite identifier.
 * - Records PersetujuanPlpCreated domain event on creation.
 */
class PersetujuanPlp extends BaseAggregateRoot
{
    /**
     * @param  array<string, PersetujuanPlpKontainer>  $kontainer
     * @param  array<string, PersetujuanPlpKemasan>  $kemasan
     */
    private function __construct(
        public readonly PersetujuanPlpId $id,
        public private(set) PersetujuanPlpIdentifier $identifier,
        public private(set) KodeKantor $kodeKantor,
        public private(set) KodeTps $kodeTpsAsal,
        public private(set) KodeTps $kodeTpsTujuan,
        public private(set) KodeGudang $gudangAsal,
        public private(set) KodeGudang $gudangTujuan,
        public private(set) SuratIdentifier $suratIdentifier,
        public private(set) Bc11Identifier $bc11Identifier,
        public private(set) ?string $callSign = null,
        public private(set) ?string $namaAngkut = null,
        public private(set) ?string $nomorVoyFlight = null,
        public private(set) ?DateTimeImmutable $tanggalTiba = null,
        public private(set) ?string $refNumber = null,
        public private(set) ?string $alasanReject = null,
        public private(set) ?int $idTdTpsPlp = null,
        private array $kontainer = [],
        private array $kemasan = [],
    ) {}

    /**
     * Factory method creating a new PersetujuanPlp aggregate root with initial state.
     *
     * @param  PersetujuanPlpKontainer[]  $kontainer
     * @param  PersetujuanPlpKemasan[]  $kemasan
     */
    public static function create(
        PersetujuanPlpIdentifier $identifier,
        KodeKantor $kodeKantor,
        KodeTps $kodeTpsAsal,
        KodeTps $kodeTpsTujuan,
        KodeGudang $gudangAsal,
        KodeGudang $gudangTujuan,
        SuratIdentifier $suratIdentifier,
        Bc11Identifier $bc11Identifier,
        ?string $callSign = null,
        ?string $namaAngkut = null,
        ?string $nomorVoyFlight = null,
        ?DateTimeImmutable $tanggalTiba = null,
        ?string $refNumber = null,
        ?string $alasanReject = null,
        ?int $idTdTpsPlp = null,
        array $kontainer = [],
        array $kemasan = [],
        PersetujuanPlpId|string|null $id = null,
    ): self {
        self::assertDifferentOriginAndDestination(
            $kodeTpsAsal,
            $kodeTpsTujuan,
            $gudangAsal,
            $gudangTujuan
        );

        $entityId = $id instanceof PersetujuanPlpId
            ? $id
            : ($id !== null ? PersetujuanPlpId::fromString((string) $id) : PersetujuanPlpId::random());

        $plp = new self(
            id: $entityId,
            identifier: $identifier,
            kodeKantor: $kodeKantor,
            kodeTpsAsal: $kodeTpsAsal,
            kodeTpsTujuan: $kodeTpsTujuan,
            gudangAsal: $gudangAsal,
            gudangTujuan: $gudangTujuan,
            suratIdentifier: $suratIdentifier,
            bc11Identifier: $bc11Identifier,
            callSign: $callSign,
            namaAngkut: $namaAngkut,
            nomorVoyFlight: $nomorVoyFlight,
            tanggalTiba: $tanggalTiba,
            refNumber: $refNumber,
            alasanReject: self::sanitizeRejectReason($alasanReject),
            idTdTpsPlp: $idTdTpsPlp,
            kontainer: [],
            kemasan: [],
        );

        $plp->setIsNew(true);
        $plp->recordEvent(new PersetujuanPlpCreated(
            id: $entityId,
            identifier: $identifier,
            kodeKantor: $kodeKantor,
            kodeTpsAsal: $kodeTpsAsal,
            kodeTpsTujuan: $kodeTpsTujuan,
        ));

        foreach ($kontainer as $item) {
            $plp->addKontainer($item);
        }

        foreach ($kemasan as $item) {
            $plp->addKemasan($item);
        }

        return $plp;
    }

    /**
     * Reconstitutes an existing aggregate root from persistence storage without raising creation events.
     *
     * @param  PersetujuanPlpKontainer[]  $kontainer
     * @param  PersetujuanPlpKemasan[]  $kemasan
     */
    public static function reconstitute(
        PersetujuanPlpId|string $id,
        PersetujuanPlpIdentifier $identifier,
        KodeKantor $kodeKantor,
        KodeTps $kodeTpsAsal,
        KodeTps $kodeTpsTujuan,
        KodeGudang $gudangAsal,
        KodeGudang $gudangTujuan,
        SuratIdentifier $suratIdentifier,
        Bc11Identifier $bc11Identifier,
        ?string $callSign = null,
        ?string $namaAngkut = null,
        ?string $nomorVoyFlight = null,
        ?DateTimeImmutable $tanggalTiba = null,
        ?string $refNumber = null,
        ?string $alasanReject = null,
        ?int $idTdTpsPlp = null,
        array $kontainer = [],
        array $kemasan = [],
        ?Timestamp $timestamps = null,
    ): self {
        $indexedKontainer = [];
        foreach ($kontainer as $item) {
            $indexedKontainer[self::normalizeContainerKey($item->nomorKontainer())] = $item;
        }

        $indexedKemasan = [];
        foreach ($kemasan as $item) {
            $indexedKemasan[$item->compositeKey()] = $item;
        }

        $resolvedId = $id instanceof PersetujuanPlpId
            ? $id
            : PersetujuanPlpId::fromString((string) $id);

        $plp = new self(
            id: $resolvedId,
            identifier: $identifier,
            kodeKantor: $kodeKantor,
            kodeTpsAsal: $kodeTpsAsal,
            kodeTpsTujuan: $kodeTpsTujuan,
            gudangAsal: $gudangAsal,
            gudangTujuan: $gudangTujuan,
            suratIdentifier: $suratIdentifier,
            bc11Identifier: $bc11Identifier,
            callSign: $callSign,
            namaAngkut: $namaAngkut,
            nomorVoyFlight: $nomorVoyFlight,
            tanggalTiba: $tanggalTiba,
            refNumber: $refNumber,
            alasanReject: self::sanitizeRejectReason($alasanReject),
            idTdTpsPlp: $idTdTpsPlp,
            kontainer: $indexedKontainer,
            kemasan: $indexedKemasan,
        );

        $plp->setIsNew(false);
        if ($timestamps !== null) {
            $plp->timestamps = $timestamps;
        }

        $plp->makeCheckPoint();

        return $plp;
    }

    /**
     * Adds a child entity PersetujuanPlpKontainer to kontainer while enforcing invariants.
     */
    public function addKontainer(PersetujuanPlpKontainer $kontainer): void
    {
        $key = self::normalizeContainerKey($kontainer->nomorKontainer());
        if (isset($this->kontainer[$key])) {
            throw PersetujuanPlpException::duplicateKontainer($kontainer->nomorKontainer()->value);
        }

        $this->kontainer[$key] = $kontainer;
    }

    /**
     * Adds a child entity PersetujuanPlpKemasan to kemasan while enforcing invariants.
     */
    public function addKemasan(PersetujuanPlpKemasan $kemasan): void
    {
        $key = $kemasan->compositeKey();
        if (isset($this->kemasan[$key])) {
            throw PersetujuanPlpException::duplicateKemasan(
                $kemasan->blAwbIdentifier()->nomorBlAwb(),
                $kemasan->nomorPosBc11()->value
            );
        }

        $this->kemasan[$key] = $kemasan;
    }

    /**
     * Removes a container item from this aggregate root.
     */
    public function removeKontainer(PersetujuanPlpKontainer|NomorKontainer|string $kontainer): void
    {
        $nomor = match (true) {
            $kontainer instanceof PersetujuanPlpKontainer => $kontainer->nomorKontainer()->value,
            $kontainer instanceof NomorKontainer => $kontainer->value,
            default => trim((string) $kontainer),
        };

        $key = strtoupper($nomor);
        if (! isset($this->kontainer[$key])) {
            throw PersetujuanPlpException::kontainerNotFound($nomor);
        }

        unset($this->kontainer[$key]);
    }

    /**
     * Removes a package item from this aggregate root.
     */
    public function removeKemasan(
        PersetujuanPlpKemasan|BlAwbIdentifier|string $blAwb,
        NomorPosBc11|string|null $nomorPosBc11 = null,
    ): void {
        if ($blAwb instanceof PersetujuanPlpKemasan) {
            $key = $blAwb->compositeKey();
            $blStr = $blAwb->blAwbIdentifier()->nomorBlAwb();
            $posStr = $blAwb->nomorPosBc11()->value;
        } else {
            $blStr = $blAwb instanceof BlAwbIdentifier ? $blAwb->nomorBlAwb() : trim((string) $blAwb);
            $posVO = $nomorPosBc11 instanceof NomorPosBc11
                ? $nomorPosBc11
                : NomorPosBc11::fromString((string) $nomorPosBc11);
            $posStr = $posVO->value;
            $key = sprintf('%s:%s', $blStr, $posStr);
        }

        if (! isset($this->kemasan[$key])) {
            throw PersetujuanPlpException::kemasanNotFound($blStr, $posStr);
        }

        unset($this->kemasan[$key]);
    }

    // Property getters
    public function id(): PersetujuanPlpId
    {
        return $this->id;
    }

    public function identifier(): PersetujuanPlpIdentifier
    {
        return $this->identifier;
    }

    public function nomorPlp(): string
    {
        return $this->identifier->nomorPlp();
    }

    public function tanggalPlp(): DateTimeImmutable
    {
        return $this->identifier->tanggalPlp();
    }

    public function kodeKantor(): KodeKantor
    {
        return $this->kodeKantor;
    }

    public function kodeTpsAsal(): KodeTps
    {
        return $this->kodeTpsAsal;
    }

    public function kodeTpsTujuan(): KodeTps
    {
        return $this->kodeTpsTujuan;
    }

    public function gudangAsal(): KodeGudang
    {
        return $this->gudangAsal;
    }

    public function gudangTujuan(): KodeGudang
    {
        return $this->gudangTujuan;
    }

    public function suratIdentifier(): SuratIdentifier
    {
        return $this->suratIdentifier;
    }

    public function nomorSurat(): string
    {
        return $this->suratIdentifier->nomorSurat();
    }

    public function tanggalSurat(): DateTimeImmutable
    {
        return $this->suratIdentifier->tanggalSurat();
    }

    public function bc11Identifier(): Bc11Identifier
    {
        return $this->bc11Identifier;
    }

    public function nomorBc11(): string
    {
        return $this->bc11Identifier->nomorBc11();
    }

    public function tanggalBc11(): DateTimeImmutable
    {
        return $this->bc11Identifier->tanggalBc11();
    }

    public function callSign(): ?string
    {
        return $this->callSign;
    }

    public function namaAngkut(): ?string
    {
        return $this->namaAngkut;
    }

    public function nomorVoyFlight(): ?string
    {
        return $this->nomorVoyFlight;
    }

    public function tanggalTiba(): ?DateTimeImmutable
    {
        return $this->tanggalTiba;
    }

    public function refNumber(): ?string
    {
        return $this->refNumber;
    }

    public function alasanReject(): ?string
    {
        return $this->alasanReject;
    }

    public function idTdTpsPlp(): ?int
    {
        return $this->idTdTpsPlp;
    }

    /**
     * @return PersetujuanPlpKontainer[]
     */
    public function kontainer(): array
    {
        return array_values($this->kontainer);
    }

    /**
     * @return PersetujuanPlpKemasan[]
     */
    public function kemasan(): array
    {
        return array_values($this->kemasan);
    }

    private static function assertDifferentOriginAndDestination(
        KodeTps $kodeTpsAsal,
        KodeTps $kodeTpsTujuan,
        KodeGudang $gudangAsal,
        KodeGudang $gudangTujuan,
    ): void {
        if ($kodeTpsAsal->equals($kodeTpsTujuan) && $gudangAsal->equals($gudangTujuan)) {
            throw PersetujuanPlpException::sameOriginAndDestination(
                $kodeTpsAsal->value,
                $gudangAsal->value
            );
        }
    }

    private static function normalizeContainerKey(NomorKontainer $nomorKontainer): string
    {
        return strtoupper($nomorKontainer->value);
    }

    private static function sanitizeRejectReason(?string $reason): ?string
    {
        if ($reason === null) {
            return null;
        }

        $trimmed = trim($reason);

        return ($trimmed === '' || $trimmed === '-') ? null : $trimmed;
    }
}
