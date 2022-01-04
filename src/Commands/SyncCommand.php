<?php

namespace Weble\DataSyncLaravel\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Weble\DataSync\Events\ItemProcessed;
use Weble\DataSync\Events\ItemSkipped;
use Weble\DataSync\Events\ResourceSynced;
use Weble\DataSync\Events\ResourceSyncing;
use Weble\DataSync\Events\SyncStarted;
use Weble\DataSync\Events\SyncStarting;
use Weble\DataSync\Sync;
use Weble\DataSyncLaravel\Facades\DataSync;

class SyncCommand extends Command
{
    public $signature = 'datasync:sync';

    public $description = 'Execute a Sync';

    private ?ProgressBar $progress = null;

    public function handle(): int
    {
        try {
            $this->listenToEvents();

            $recipes = DataSync::recipes();
            $recipe = $this->chooseRecipe($recipes);
            if ($recipe === "All") {
                foreach ($recipes as $recipe) {
                    \Weble\DataSync\DataSync::startSync($recipe);
                    return self::SUCCESS;
                }
            }

            \Weble\DataSync\DataSync::startSync($recipe);
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->output->error($e->getMessage());

            throw $e;
        }
    }

    private function chooseRecipe(array $recipes): string
    {
        if (count($recipes) <= 0) {
            throw new \Exception("There are no recipes available");
        }

        array_unshift($recipes, "All");

        return $this->choice("Which Recipe would you like to run?", $recipes);
    }

    private function listenToEvents(): void
    {
        DataSync::listen([
            SyncStarting::NAME    => [
                fn(SyncStarting $event) => $this->output->writeln("Starting Sync for Recipe: " . $event->sync()->name)
            ],
            SyncStarted::NAME     => [
                fn(SyncStarted $event) => $this->output->writeln("Started Sync for Recipe: " . $event->sync()->name)
            ],
            ResourceSyncing::NAME => [
                function (ResourceSyncing $event) {
                    if ($event->resource() instanceof \Countable) {
                        $this->progress = $this->getOutput()->createProgressBar($event->resource()->count());
                    }

                    $this->output->writeln("Started Syncing Resource: ");
                }
            ],
            ResourceSynced::NAME  => [
                function (ResourceSynced $event) {
                    if ($event->resource() instanceof \Countable && $this->progress) {
                        $this->progress->finish();
                    }
                    $this->output->writeln("Finished Syncing Resource: ");
                }
            ],
            ItemProcessed::NAME   => [
                function (ItemProcessed $event) {

                    if ($this->progress) {
                        $this->progress->advance();
                    }

                    $this->output->writeln("Finished Syncing Item: ");
                }
            ],
            ItemSkipped::NAME     => [
                function (ItemSkipped $event) {

                    if ($this->progress) {
                        $this->progress->advance();
                    }

                    $this->output->writeln("Skipped Syncing Item: ");
                }
            ]
        ]);
    }
}
