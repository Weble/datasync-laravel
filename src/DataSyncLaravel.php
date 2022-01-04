<?php

namespace Weble\DataSyncLaravel;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Weble\DataSyncLaravel\Support\DiscoverSyncRecipes;

class DataSyncLaravel
{
    private array $recipes = [];
    private EventDispatcher $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function recipes(): array
    {
        if (!empty($this->recipes)) {
            return $this->recipes;
        }
        $discoveredRecipes = DiscoverSyncRecipes::within([app_path('SyncRecipes')]);
        return $this->recipes = array_merge($discoveredRecipes, config('datasync.recipes', []));
    }

    public function listen(array $listeners): void
    {
        foreach ($listeners as $eventName => $eventListeners) {
            foreach ($eventListeners as $eventListener) {
               $this->eventDispatcher->addListener($eventName, $eventListener);
            }
        }
    }
}
