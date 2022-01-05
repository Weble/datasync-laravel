<?php

namespace Weble\DataSyncLaravel\Tests\Support;

use Illuminate\Support\Facades\Storage;
use Weble\DataSync\Contracts\IngredientInterface;
use Weble\DataSync\Sync;

class DownloadFilesIngredient implements IngredientInterface
{
    public function prepare(Sync $sync): void
    {
        $source = Storage::disk('source');
        $target = Storage::disk('target');

        collect($source->files())->each(fn ($file) => $target->put($file, $source->readStream($file)));
    }
}
