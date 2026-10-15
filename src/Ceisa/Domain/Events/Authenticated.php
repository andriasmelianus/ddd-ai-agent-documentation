<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Events;

use Src\Ceisa\Domain\ValueObjects\AuthCredentials;
use Src\Ceisa\Domain\ValueObjects\AuthLoginResult;
use Src\Ceisa\Domain\ValueObjects\KodeGudang;
use Src\Shared\Framework\Domain\Events\DomainEvent;

final class Authenticated extends DomainEvent
{
    public readonly KodeGudang $warehouseCode;

    public function __construct(
        public readonly KodeGudang $kodeGudang,
        public readonly AuthCredentials $credentials,
        public readonly ?AuthLoginResult $loginResult = null,
    ) {
        $this->warehouseCode = $this->kodeGudang;
        parent::__construct();
    }

    public function getName(): string
    {
        return 'ceisa40.authenticated';
    }
}
