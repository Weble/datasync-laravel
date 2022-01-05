<?php

namespace Weble\DataSyncLaravel;

use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Weble\DataSync\Contracts\EngineInterface;
use Weble\DataSync\DataSync;
use Weble\DataSync\Engine;
use Weble\DataSync\Pipeline\ProcessPipeline;
use Weble\DataSyncLaravel\Commands\MakeSyncRecipeCommand;
use Weble\DataSyncLaravel\Commands\SyncCommand;
use Weble\DataSyncLaravel\Commands\SyncListCommand;

class DataSyncServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('datasynclaravel')
            ->hasConfigFile('datasync')
            //->hasViews()
            //->hasMigration('create_datasync_table')
            ->hasCommands([
                SyncCommand::class,
                SyncListCommand::class,
                MakeSyncRecipeCommand::class,
            ]);
    }

    public function registeringPackage(): void
    {
        $this->app->bind(Engine::class, Engine::class);
        $this->app->bind(EngineInterface::class, Engine::class);
        $this->app->singleton(EventDispatcher::class, EventDispatcher::class);
        $this->app->singleton(EventDispatcherInterface::class, EventDispatcher::class);
        $this->app->singleton(\Psr\EventDispatcher\EventDispatcherInterface::class, EventDispatcher::class);
        $this->app->bind(ProcessPipeline::class);

        $this->app->singleton(DataSyncLaravel::class, DataSyncLaravel::class);
        $this->app->singleton('datasync', DataSyncLaravel::class);

        $this->proxyEvents();
    }

    public function bootingPackage(): void
    {
        DataSync::useContainer($this->app);
    }

    private function proxyEvents(): void
    {
        /** @var EventDispatcher $coreDispatcher */
        $coreDispatcher = $this->app->get(EventDispatcherInterface::class);
        foreach (DataSync::events() as $eventName) {
            $coreDispatcher->addListener($eventName, fn($event) => Event::dispatch($eventName, $event));
        }
    }
}
