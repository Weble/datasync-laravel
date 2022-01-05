<?php

namespace Weble\DataSyncLaravel\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Weble\DataSync\Contracts\ProgressibleInterface;
use Weble\DataSync\Events\ItemProcessed;
use Weble\DataSync\Events\ItemSkipped;
use Weble\DataSync\Events\ResourceSynced;
use Weble\DataSync\Events\ResourceSyncing;
use Weble\DataSync\Events\SyncStarted;
use Weble\DataSync\Events\SyncStarting;
use Weble\DataSyncLaravel\DataSyncLaravel;
use Weble\DataSyncLaravel\Facades\DataSync;

class SyncCommand extends Command
{
    private const OPTION_ALL = "All";

    public $signature = 'datasync:sync {recipe?}';

    public $description = 'Execute a Sync';

    private ?ProgressBar $progress = null;

    public function handle(): int
    {
        try {
            $this->listenToEvents();

            $recipes = DataSync::recipes();

            $recipe = $this->argument('recipe');
            if (!$recipe) {
                $recipe = $this->chooseRecipe($recipes);
            }

            if ($recipe === self::OPTION_ALL) {
                foreach ($recipes as $recipe) {
                    \Weble\DataSync\DataSync::startSync($recipe);

                    return self::SUCCESS;
                }
            }

            if (!in_array($recipe, $recipes)) {
                $recipe = $this->guessRecipeClassFromName($recipe, $recipes);
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

        array_unshift($recipes, self::OPTION_ALL);

        return $this->choice("Which Recipe would you like to run?", $recipes);
    }

    private function listenToEvents(): void
    {
        DataSync::listen([
            SyncStarting::NAME => [
                fn (SyncStarting $event) => $this->output->writeln("Starting Sync for Recipe: " . $event->sync()->name),
            ],
            SyncStarted::NAME => [
                fn (SyncStarted $event) => $this->output->writeln("Started Sync for Recipe: " . $event->sync()->name),
            ],
            ResourceSyncing::NAME => [
                function (ResourceSyncing $event) {
                    if ($event->resource() instanceof \Countable) {
                        $this->progress = $this->getOutput()->createProgressBar($event->resource()->count());
                    }

                    if ($event->resource() instanceof ProgressibleInterface && $event->resource()->progressTotal()) {
                        $this->progress = $this->getOutput()->createProgressBar($event->resource()->progressTotal());
                        $event->resource()->progressCallback(function($progress) {
                            $this->progress->advance($progress);
                        });
                    }
                },
            ],
            ResourceSynced::NAME => [
                function (ResourceSynced $event) {
                    if ($event->resource() instanceof \Countable && $this->progress) {
                        $this->progress->finish();
                    }
                },
            ],
            ItemProcessed::NAME => [
                function (ItemProcessed $event) {
                    if ($this->progress) {
                        $this->progress->advance();
                    }
                },
            ],
            ItemSkipped::NAME => [
                function (ItemSkipped $event) {
                    if ($this->progress) {
                        $this->progress->advance();
                    }
                },
            ],
        ]);
    }

    private function guessRecipeClassFromName(mixed $recipe, array $recipes): string
    {
        $simpleClass = substr($recipe, strrpos("\\", $recipe));
        $fullClass = app()->getNamespace() . DataSyncLaravel::DEFAULT_FOLDER . '\\' . $simpleClass;

        if (!in_array($fullClass, $recipes)) {
            throw new \Exception("Sync Recipe {$recipe} not found");
        }

        return $fullClass;
    }
}
