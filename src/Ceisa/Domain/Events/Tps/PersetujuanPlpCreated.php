<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Events\Tps;

use Src\Ceisa\Domain\ValueObjects\KodeKantor;
use Src\Ceisa\Domain\ValueObjects\KodeTps;
use Src\Ceisa\Domain\ValueObjects\Tps\PersetujuanPlpId;
use Src\Ceisa\Domain\ValueObjects\Tps\PersetujuanPlpIdentifier;
use Src\Shared\Framework\Domain\Events\DomainEvent;

final class PersetujuanPlpCreated extends DomainEvent
{
    public function __construct(
        public readonly PersetujuanPlpId $id,
        public readonly PersetujuanPlpIdentifier $identifier,
        public readonly KodeKantor $kodeKantor,
        public readonly KodeTps $kodeTpsAsal,
        public readonly KodeTps $kodeTpsTujuan,
    ) {
        parent::__construct();
    }

    public function getName(): string
    {
        return 'ceisa40.plp.persetujuan_created';
    }
}

