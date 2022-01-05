<?php

namespace Weble\DataSyncLaravel\Commands;

use Illuminate\Console\Command;
use Weble\DataSyncLaravel\Facades\DataSync;

class SyncListCommand extends Command
{
    public $signature = 'datasync:list';

    public $description = 'List all registered Syncs';

    public function handle(): int
    {
        $recipes = DataSync::recipes();

        $this->table(['Name', 'Class'], array_map(fn($r) => [app($r)->name(), $r], $recipes));

        return self::SUCCESS;
    }
}
