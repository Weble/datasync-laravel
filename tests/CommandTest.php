<?php

use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\artisan;
use Weble\DataSyncLaravel\Commands\SyncCommand;
use Weble\DataSyncLaravel\Commands\SyncListCommand;
use Weble\DataSyncLaravel\Tests\Support\CountProcessor;
use Weble\DataSyncLaravel\Tests\Syncs\TestRecipe;

beforeEach(fn () => CountProcessor::clear() && Storage::disk('target')->delete('countries.csv'));

it('can list the syncs', function () {
    artisan(SyncListCommand::class)
        ->expectsTable(["Name", "Class"], [["Test", TestRecipe::class]]);
});

it('asks for which sync to run', function () {
    artisan(SyncCommand::class)
        ->expectsChoice("Which Recipe would you like to run?", TestRecipe::class, [
           "All",
           TestRecipe::class,
        ]);
});

it('can execute a sync', function () {
    artisan(SyncCommand::class)
        ->expectsChoice("Which Recipe would you like to run?", TestRecipe::class, [
            "All",
            TestRecipe::class,
        ])->expectsOutput("Starting Sync for Recipe: Test");

    expect(CountProcessor::count())->toBe(245);
});
