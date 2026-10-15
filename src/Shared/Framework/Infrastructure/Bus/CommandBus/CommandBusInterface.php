<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Bus\CommandBus;

use Src\Shared\Framework\Application\Commands\CommandInterface;

/**
 * Kontrak untuk sebuah Command Bus.
 *
 * Command Bus bertugas menerima objek {@see CommandInterface} dan mengarahkannya
 * ke handler yang sesuai (sinkron / asinkron via queue). Interface ini
 * mendefinisikan sekumpulan metode dispatch dengan variasi perilaku:
 *  - dispatch biasa (opsional async),
 *  - dispatch untuk "process command" (naming convention terpisah),
 *  - dispatch async paksa,
 *  - dispatch dengan penekanan exception tertentu.
 */
interface CommandBusInterface
{
    /**
     * Mendistribusikan command ke handler yang sesuai.
     *
     * Jika `$async = true`, eksekusi handler diserahkan ke queue worker.
     *
     * @param  CommandInterface  $command  Command yang akan dieksekusi.
     * @param  bool  $async  True untuk memaksa eksekusi asinkron.
     */
    public function dispatch(CommandInterface $command, bool $async = false): void;

    /**
     * Mendistribusikan "process" command ke handler yang sesuai.
     *
     * Perbedaan utama dengan {@see self::dispatch()} adalah konvensi penamaan
     * handler (`XxxProcess` → `XxxProcessHandler`), sehingga cocok untuk
     * command yang merepresentasikan proses berjenjang.
     *
     * @param  CommandInterface  $process  Command bertipe "process".
     * @param  bool  $async  True untuk memaksa eksekusi asinkron.
     */
    public function dispatchProcess(CommandInterface $process, bool $async = false): void;

    /**
     * Shortcut untuk mendispatch command secara asinkron (via queue).
     *
     * @param  CommandInterface  $command  Command yang akan diproses di queue.
     */
    public function dispatchAsync(CommandInterface $command): void;

    /**
     * Mendispatch command dan menekan satu exception class tertentu bila terjadi.
     *
     * @param  CommandInterface  $command  Command yang akan dieksekusi.
     * @param  string  $exceptionClass  FQCN exception yang ditekan.
     */
    public function dispatchIgnoreException(CommandInterface $command, string $exceptionClass): void;

    /**
     * Mendispatch command dan menekan kumpulan exception class tertentu bila terjadi.
     *
     * @param  CommandInterface  $command  Command yang akan dieksekusi.
     * @param  array<string>  $exceptionClasses  Daftar FQCN exception yang ditekan.
     */
    public function dispatchIgnoreExceptions(CommandInterface $command, array $exceptionClasses): void;
}
