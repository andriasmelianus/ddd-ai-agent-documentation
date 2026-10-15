<?php

declare(strict_types=1);

namespace Src\Partner\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Partner\Domain\Repositories\ConsigneeQueryInterface;
use Src\Partner\Domain\Repositories\ConsigneeRepositoryInterface;
use Src\Partner\Domain\Repositories\ForwarderQueryInterface;
use Src\Partner\Domain\Repositories\ForwarderRepositoryInterface;
use Src\Partner\Domain\Repositories\PpjkQueryInterface;
use Src\Partner\Domain\Repositories\PpjkRepositoryInterface;
use Src\Partner\Infrastructure\Mappers\ConsigneeMapper;
use Src\Partner\Infrastructure\Mappers\ForwarderMapper;
use Src\Partner\Infrastructure\Mappers\PpjkMapper;
use Src\Partner\Infrastructure\Persistence\ConsigneeQuery;
use Src\Partner\Infrastructure\Persistence\ConsigneeRepository;
use Src\Partner\Infrastructure\Persistence\ForwarderQuery;
use Src\Partner\Infrastructure\Persistence\ForwarderRepository;
use Src\Partner\Infrastructure\Persistence\PpjkQuery;
use Src\Partner\Infrastructure\Persistence\PpjkRepository;
use Src\Shared\Framework\Infrastructure\Bus\EventBus\EventBusInterface;

final class PartnerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Mappers
        $this->app->singleton(ConsigneeMapper::class);
        $this->app->singleton(ForwarderMapper::class);
        $this->app->singleton(PpjkMapper::class);

        // Consignee CQRS Ports
        $this->app->bind(ConsigneeRepositoryInterface::class, function ($app) {
            return new ConsigneeRepository(
                mapper: $app->make(ConsigneeMapper::class),
                eventBus: $app->bound(EventBusInterface::class) ? $app->make(EventBusInterface::class) : null,
            );
        });

        $this->app->bind(ConsigneeQueryInterface::class, function ($app) {
            return new ConsigneeQuery(
                mapper: $app->make(ConsigneeMapper::class),
            );
        });

        // Forwarder CQRS Ports
        $this->app->bind(ForwarderRepositoryInterface::class, function ($app) {
            return new ForwarderRepository(
                mapper: $app->make(ForwarderMapper::class),
                eventBus: $app->bound(EventBusInterface::class) ? $app->make(EventBusInterface::class) : null,
            );
        });

        $this->app->bind(ForwarderQueryInterface::class, function ($app) {
            return new ForwarderQuery(
                mapper: $app->make(ForwarderMapper::class),
            );
        });

        // PPJK CQRS Ports
        $this->app->bind(PpjkRepositoryInterface::class, function ($app) {
            return new PpjkRepository(
                mapper: $app->make(PpjkMapper::class),
                eventBus: $app->bound(EventBusInterface::class) ? $app->make(EventBusInterface::class) : null,
            );
        });

        $this->app->bind(PpjkQueryInterface::class, function ($app) {
            return new PpjkQuery(
                mapper: $app->make(PpjkMapper::class),
            );
        });

        // Presentation ResServices
        $this->app->bind(
            \Features\Api\Consignee\Shared\Services\ConsigneeResServiceInterface::class,
            \Features\Api\Consignee\Shared\Services\ConsigneeResService::class
        );
        $this->app->bind(
            \Features\Api\Forwarder\Shared\Services\ForwarderResServiceInterface::class,
            \Features\Api\Forwarder\Shared\Services\ForwarderResService::class
        );
        $this->app->bind(
            \Features\Api\Ppjk\Shared\Services\PpjkResServiceInterface::class,
            \Features\Api\Ppjk\Shared\Services\PpjkResService::class
        );
    }
}
