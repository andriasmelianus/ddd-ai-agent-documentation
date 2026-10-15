<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Shared\Framework\Infrastructure\Bus\CommandBus\CommandBusInterface;
use Src\Shared\Framework\Infrastructure\Bus\CommandBus\SimpleCommandBus;
use Src\Shared\Framework\Infrastructure\Bus\EventBus\EventBusInterface;
use Src\Shared\Framework\Infrastructure\Bus\EventBus\SimpleEventBus;
use Src\Shared\Framework\Infrastructure\Bus\ProjectionBus\ProjectionBusInterface;
use Src\Shared\Framework\Infrastructure\Bus\ProjectionBus\SimpleProjectionBus;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryBus;
use Src\Shared\Framework\Infrastructure\Bus\QueryBus\QueryBusInterface;

final class SharedServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CommandBusInterface::class, SimpleCommandBus::class);
        $this->app->singleton(QueryBusInterface::class, QueryBus::class);
        $this->app->singleton(EventBusInterface::class, SimpleEventBus::class);
        $this->app->singleton(ProjectionBusInterface::class, SimpleProjectionBus::class);
    }
}
