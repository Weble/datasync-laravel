<?php

namespace Weble\DataSyncLaravel\Tests\Syncs;

use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use Weble\DataSync\Contracts\SyncRecipeInterface;
use Weble\DataSync\Resource\Csv\CsvResource;
use Weble\DataSync\SyncRecipe;
use Weble\DataSyncLaravel\Tests\Support\CodeLabelTransformer;
use Weble\DataSyncLaravel\Tests\Support\CountProcessor;
use Weble\DataSyncLaravel\Tests\Support\DownloadFilesIngredient;

class TestRecipe extends SyncRecipe implements SyncRecipeInterface
{
    public function name(): string
    {
        return "Test";
    }

    public function resources(): \Generator
    {
        $disk = Storage::disk('target');

        foreach ($disk->files() as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'csv') {
                continue;
            }

            yield new CsvResource(
                Reader::createFromStream($disk->readStream($file))
                    ->setHeaderOffset(0)
            );
        }
    }

    public function processors(): array
    {
        return [
            CodeLabelTransformer::class,
            CountProcessor::class
        ];
    }

    public function ingredients(): array
    {
        return [
            DownloadFilesIngredient::class
        ];
    }
}
