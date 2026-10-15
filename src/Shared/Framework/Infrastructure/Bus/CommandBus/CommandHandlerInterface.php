<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Bus\CommandBus;

use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * Kontrak untuk handler dari sebuah {@see CommandInterface}.
 *
 * Handler diimplementasikan sebagai callable object (invokable class): setiap
 * class yang mengimplementasikan interface ini harus menyediakan metode
 * magis `__invoke(CommandInterface $command)` untuk menjalankan logika bisnis.
 *
 * Interface ini sengaja dibiarkan kosong; kontrak eksekusi dinyatakan lewat
 * anotasi `@method __invoke` sehingga tetap terdokumentasi bagi IDE/static
 * analyzer tanpa memaksa signature strict (agar handler bebas mengetikkan
 * command turunan yang lebih spesifik).
 *
 * @method __invoke(CommandInterface $command)
 */
interface CommandHandlerInterface {}
