<?php

namespace Weble\DataSyncLaravel\Tests\Syncs;

use Illuminate\Support\Facades\Storage;
use Weble\DataSync\Contracts\SyncRecipeInterface;
use Weble\DataSync\Resource\CsvStreamResource;
use Weble\DataSyncLaravel\Tests\Support\CodeLabelTransformer;
use Weble\DataSyncLaravel\Tests\Support\CountProcessor;

class TestRecipe implements SyncRecipeInterface
{
    public function name(): string
    {
        return "Test";
    }

    public function resources(): \Generator
    {
        $disk = Storage::disk('source');

        foreach ($disk->files() as $file) {
            yield new CsvStreamResource(
                $disk->readStream($file)
            );
        }
    }

    public function transformers(): array
    {
        return [
            CodeLabelTransformer::class,
        ];
    }

    public function processors(): array
    {
        return [
            CountProcessor::class,
        ];
    }
}
