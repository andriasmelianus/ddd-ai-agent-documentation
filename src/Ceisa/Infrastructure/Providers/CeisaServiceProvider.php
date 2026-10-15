<?php

declare(strict_types=1);

namespace Src\Ceisa\Infrastructure\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Src\Ceisa\Application\EventListeners\StoreAuthLoginResultOnAuthenticated;
use Src\Ceisa\Domain\Events\Authenticated;
use Src\Ceisa\Domain\Repositories\AuthRepositoryInterface;
use Src\Ceisa\Domain\Services\AuthServiceInterface;
use Src\Ceisa\Infrastructure\Gateway\AuthServiceAdapter;
use Src\Ceisa\Infrastructure\Persistence\AuthRepositoryAdapter;

final class CeisaServiceProvider extends ServiceProvider
{
    /**
     * Register Ceisa domain services and infrastructure bindings.
     */
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthServiceAdapter::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepositoryAdapter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            Authenticated::class,
            StoreAuthLoginResultOnAuthenticated::class,
        );
    }
}

class_alias(CeisaServiceProvider::class, 'Src\Ceisa\Infrastructure\Providers\Ceisa40ServiceProvider');
class_alias(CeisaServiceProvider::class, 'Src\Ceisa40\Infrastructure\Providers\Ceisa40ServiceProvider');

