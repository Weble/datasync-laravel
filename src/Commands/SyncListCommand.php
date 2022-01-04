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

        $this->table(['Class'], [$recipes]);

        return self::SUCCESS;
    }
}
