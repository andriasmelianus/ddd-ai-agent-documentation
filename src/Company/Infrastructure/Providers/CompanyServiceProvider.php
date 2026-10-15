<?php

declare(strict_types=1);

namespace Src\Company\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Company\Domain\Repositories\CompanyQueryInterface;
use Src\Company\Domain\Repositories\CompanyRepositoryInterface;
use Src\Company\Infrastructure\Mappers\CompanyMapper;
use Src\Company\Infrastructure\Persistence\CompanyQuery;
use Src\Company\Infrastructure\Persistence\CompanyRepository;
use Src\Shared\Framework\Infrastructure\Bus\EventBus\EventBusInterface;

final class CompanyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CompanyMapper::class);

        $this->app->bind(CompanyRepositoryInterface::class, function ($app) {
            return new CompanyRepository(
                mapper: $app->make(CompanyMapper::class),
                eventBus: $app->bound(EventBusInterface::class) ? $app->make(EventBusInterface::class) : null,
            );
        });

        $this->app->bind(CompanyQueryInterface::class, function ($app) {
            return new CompanyQuery(
                mapper: $app->make(CompanyMapper::class),
            );
        });

        $this->app->bind(
            \Features\Api\Company\Shared\Services\CompanyResServiceInterface::class,
            \Features\Api\Company\Shared\Services\CompanyResService::class
        );
    }
}
