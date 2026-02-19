<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor;

use Callcocam\LaravelRaptor\Commands\LaravelRaptorCommand;
use Callcocam\LaravelRaptor\Commands\SyncPermissionsCommand;
use Callcocam\LaravelRaptor\Support\Landlord\LandlordServiceProvider;
use Callcocam\LaravelRaptor\Support\Shinobi\ShinobiServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelRaptorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-raptor')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations([
                'create_tenants_table',
                'create_tenant_domains_table', 
                'create_roles_table',
                'create_role_user_table',
                'create_permissions_table',
                'create_permission_role_table',
                'create_permission_user_table',
            ])
            ->hasCommand(LaravelRaptorCommand::class)
            ->hasCommand(SyncPermissionsCommand::class)
            ->hasInstallCommand(function (InstallCommand $command) {
               /* TODO: Implementar o comando de instalação */ 
               $command->publishMigrations()->askToRunMigrations();
            });
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(LaravelRaptor::class);
        $this->app->register(LandlordServiceProvider::class);
        $this->app->register(ShinobiServiceProvider::class);
    }
}
