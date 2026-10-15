<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Application\Commands;

/**
 * Marker interface untuk seluruh Command dalam arsitektur CQRS.
 *
 * Command merepresentasikan intent (niat) untuk mengubah state sistem
 * (misal: membuat, memperbarui, atau menghapus data). Command tidak
 * mengembalikan data domain; hasil eksekusinya dianggap sebagai efek samping
 * yang ditangani oleh {@see CommandHandlerInterface} yang bersangkutan.
 *
 * Interface ini sengaja dibiarkan kosong (marker interface) sehingga setiap
 * DTO command konkret hanya perlu meng-implement interface ini agar dapat
 * dikirim melalui {@see \Src\Shared\Framework\Infrastructure\Bus\CommandBus\CommandBusInterface}.
 */
interface CommandInterface {}
