<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Landlord;

use Callcocam\LaravelRaptor\Contracts\TenantResolverInterface;
use Callcocam\LaravelRaptor\Services\TenantDatabaseManager;
use Callcocam\LaravelRaptor\Services\TenantResolver;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

/**
 * Service Provider responsável pelo gerenciamento de multi-tenancy (landlord).
 *
 * A conexão "landlord" nunca muda — sempre aponta para o banco principal.
 * A conexão "default" é alterada para o banco do tenant quando preenchido.
 *
 * Só é ativado quando config('raptor.multi_tenant.enabled') === true.
 */
class LandlordServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerRequestMacros();

        if (! config('raptor.multi_tenant.enabled')) {
            return;
        }

        $this->resolveCurrentTenant();
    }

    public function register(): void
    {
        $this->app->singleton(TenantManager::class);
        $this->app->singleton(TenantDatabaseManager::class);

        if (! config('raptor.multi_tenant.enabled')) {
            return;
        }

        $resolverClass = config(
            'raptor.multi_tenant.resolver',
            TenantResolver::class
        );

        $this->app->singleton(TenantResolverInterface::class, $resolverClass);

        $this->app->singleton('tenant', function ($app) {
            $resolver = $app->make(TenantResolverInterface::class);

            return $resolver->getTenant();
        });

        $this->app->bind('current.tenant', fn () => app('tenant'));
    }

    /**
     * Registra macros no Request, disponíveis mesmo sem multi-tenant.
     */
    protected function registerRequestMacros(): void
    {
        Request::macro('isTenant', function (): bool {
            return config('raptor.multi_tenant.enabled', false)
                && config('app.context') !== 'landlord';
        });

        Request::macro('isLandlord', function (): bool {
            return config('app.context') === 'landlord';
        });
    }

    protected function resolveCurrentTenant(): void
    {
        $request = request();

        if (! $request || ! $request->isTenant()) {
            return;
        }

        $resolver = app(TenantResolverInterface::class);
        $resolver->resolve($request);
    }

    public function getModel(): string
    {
        return config('raptor.models.tenant', \Callcocam\LaravelRaptor\Models\Tenant::class);
    }

    public function getCurrentTenant(): mixed
    {
        return app('tenant');
    }

    public function setCurrentTenant(mixed $tenant): void
    {
        app()->instance('tenant', $tenant);
    }
}
