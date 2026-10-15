<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Bus\CommandBus;

use Closure;
use RuntimeException;
use Src\Shared\Framework\Application\Commands\CommandInterface;
use Src\Shared\Framework\Application\Commands\CommandJobInterface;
use Src\Shared\Framework\Helpers\Container;
use Throwable;

use function get_class;

/**
 * Implementasi sederhana dari {@see CommandBusInterface}.
 *
 * Strategi resolusi handler:
 *  1. Jika class command terdaftar pada {@see self::$routes}, gunakan nilai
 *     terdaftar (bisa berupa class-name handler atau {@see Closure}).
 *  2. Jika tidak, terapkan konvensi penamaan: `XxxCommand` → `XxxHandler`
 *     (atau `XxxProcess` → `XxxProcessHandler` pada {@see self::dispatchProcess()}).
 *
 * Command yang mengimplementasikan {@see CommandJobInterface} dan mengembalikan
 * `shouldQueue() = true` akan dipaksa berjalan asinkron melalui {@see QueueHandler}.
 * Handler asinkron dijalankan dengan konfigurasi `onQueue()` dan `delay()` yang
 * diambil dari command.
 */
final class SimpleCommandBus implements CommandBusInterface
{
    /**
     * Peta routing manual dari command class ke handler / closure.
     *
     * @var array<string|Closure>
     */
    public static array $routes = [];

    /**
     * {@inheritDoc}
     *
     * @throws RuntimeException Bila handler untuk command tidak dapat ditemukan.
     */
    public function dispatch(CommandInterface $command, bool $async = false): void
    {
        $commandClass = get_class($command);
        /** @var class-string<CommandHandlerInterface>|null|Closure $commandHandlerName */
        $commandHandlerName = self::$routes[$commandClass] ?? preg_replace('/Command$/', 'Handler', $commandClass);
        if ($commandHandlerName === null) {
            throw new RuntimeException('Handler not found for '.$commandClass);
        }
        if ($commandHandlerName instanceof Closure) {
            $commandHandlerName($command);

            return;
        }
        if ($command instanceof CommandJobInterface && $command->shouldQueue()) {
            $async = true;
        }
        if ($async === false) {
            /** @var CommandHandlerInterface $commandHandler */
            $commandHandler = Container::getObjectInstance($commandHandlerName);
            $commandHandler->__invoke($command);

            return;
        }

        if ($command instanceof CommandJobInterface) {
            QueueHandler::dispatch($commandHandlerName, $command)->onQueue($command->getQueue()->value)->delay($command->getDelay());

            return;
        }
        QueueHandler::dispatch($commandHandlerName, $command);
    }

    /**
     * {@inheritDoc}
     *
     * Menggunakan konvensi penamaan `XxxProcess` → `XxxProcessHandler`.
     *
     * @throws RuntimeException Bila handler untuk process tidak dapat ditemukan.
     */
    public function dispatchProcess(CommandInterface $process, bool $async = false): void
    {
        $commandClass = get_class($process);
        /** @var class-string<CommandHandlerInterface>|null|Closure $processHandlerName */
        $processHandlerName = self::$routes[$commandClass] ?? preg_replace('/Process$/', 'ProcessHandler', $commandClass);
        if ($processHandlerName === null) {
            throw new RuntimeException('Handler not found for '.$commandClass);
        }
        if ($processHandlerName instanceof Closure) {
            $processHandlerName($process);

            return;
        }
        if ($process instanceof CommandJobInterface && $process->shouldQueue()) {
            $async = true;
        }
        if ($async === false) {
            /** @var CommandHandlerInterface $processHandler */
            $processHandler = Container::getObjectInstance($processHandlerName);
            $processHandler->__invoke($process);

            return;
        }

        if ($process instanceof CommandJobInterface) {
            QueueHandler::dispatch($processHandlerName, $process)->onQueue($process->getQueue()->value)->delay($process->getDelay());

            return;
        }
        QueueHandler::dispatch($processHandlerName, $process);
    }

    /**
     * {@inheritDoc}
     */
    public function dispatchAsync(CommandInterface $command): void
    {
        $this->dispatch($command, async: true);
    }

    /**
     * {@inheritDoc}
     *
     * @throws Throwable Ketika exception yang dilempar bukan turunan `$exceptionClass`.
     */
    public function dispatchIgnoreException(CommandInterface $command, string $exceptionClass): void
    {
        try {
            $this->dispatch($command);
        } catch (Throwable $e) {
            if ($e instanceof $exceptionClass) {
                return;
            }
            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param  array<string>  $exceptionClasses
     *
     * @throws Throwable Ketika exception yang dilempar tidak masuk daftar `$exceptionClasses`.
     */
    public function dispatchIgnoreExceptions(CommandInterface $command, array $exceptionClasses): void
    {
        try {
            $this->dispatch($command);
        } catch (Throwable $e) {
            foreach ($exceptionClasses as $exceptionClass) {
                if ($e instanceof $exceptionClass) {
                    return;
                }
            }
            throw $e;
        }
    }
}
