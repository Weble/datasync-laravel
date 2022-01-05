<?php

use Illuminate\Support\Facades\Event;
use Weble\DataSync\DataSync;
use Weble\DataSync\Events\ItemProcessed;
use Weble\DataSync\Events\ItemProcessing;
use Weble\DataSync\Events\ItemSkipped;
use Weble\DataSync\Events\ResourceSynced;
use Weble\DataSync\Events\ResourceSyncing;
use Weble\DataSync\Events\SyncStarted;
use Weble\DataSync\Events\SyncStarting;
use Weble\DataSyncLaravel\Tests\Syncs\TestRecipe;

it('dispatches sync events', function () {
    Event::fake();

    DataSync::startSync(
        TestRecipe::class
    );

    Event::assertDispatched(SyncStarting::NAME, fn(string $eventName, SyncStarting $event) => $event->sync()->name === 'Test');
    Event::assertDispatched(SyncStarted::NAME, fn(string $eventName, SyncStarted $event) => $event->sync()->name === 'Test');
    Event::assertDispatched(ResourceSyncing::NAME);
    Event::assertDispatched(ResourceSynced::NAME);
    Event::assertDispatched(ItemProcessing::NAME, 246);
    Event::assertDispatched(ItemSkipped::NAME, 1);
    Event::assertDispatched(ItemProcessed::NAME, 245);
});
