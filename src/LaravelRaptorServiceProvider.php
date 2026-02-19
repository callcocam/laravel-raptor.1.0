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
            ->hasMigration('create_laravel_raptor_table')
            ->hasCommand(LaravelRaptorCommand::class)
            ->hasCommand(SyncPermissionsCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(LaravelRaptor::class);
        $this->app->register(LandlordServiceProvider::class);
        $this->app->register(ShinobiServiceProvider::class);
    }
}
