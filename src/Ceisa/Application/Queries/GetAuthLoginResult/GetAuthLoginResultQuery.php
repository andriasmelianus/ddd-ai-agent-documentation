<?php

declare(strict_types=1);

namespace Src\Ceisa\Application\Queries\GetAuthLoginResult;

use Src\Ceisa\Application\Shared\AuthLoginResultDto;
use Src\Ceisa\Domain\ValueObjects\KodeGudang;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryInterface;

/**
 * @see GetAuthLoginResultHandler
 *
 * @implements QueryInterface<AuthLoginResultDto>
 */
final readonly class GetAuthLoginResultQuery implements QueryInterface
{
    public KodeGudang $warehouseCode;

    public function __construct(
        public KodeGudang $kodeGudang,
    ) {
        $this->warehouseCode = $this->kodeGudang;
    }
}

