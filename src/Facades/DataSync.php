<?php

namespace Weble\DataSyncLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Weble\DataSync\DataSync
 *
 * @method array recipes()
 * @method array listen(array $listeners)
 */
class DataSync extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'datasync';
    }
}
