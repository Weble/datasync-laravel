<?php

namespace Weble\DataSyncLaravel\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Weble\DataSyncLaravel\DataSyncServiceProvider;
use Weble\DataSyncLaravel\Tests\Syncs\TestRecipe;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Weble\\DataSync\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            DataSyncServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('datasync.recipes', [
            TestRecipe::class
        ]);

        config()->set('filesystems.disks.source',  [
            'driver' => 'local',
            'root' => __DIR__ . '/disks/source'
        ]);

        config()->set('filesystems.disks.target',  [
            'driver' => 'local',
            'root' => __DIR__ . '/disks/target'
        ]);

        /*
        $migration = include __DIR__.'/../database/migrations/create_datasync_table.php.stub';
        $migration->up();
        */
    }
}
