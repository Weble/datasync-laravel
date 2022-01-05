<?php

namespace Weble\DataSyncLaravel\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeItemProcessorCommand extends GeneratorCommand
{
    protected $name = 'datasync:processor';
    protected $description = 'Create a new Item Processor class';
    protected $type = 'ItemProcessor';

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/recipe.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\DataSync\ItemProcessors';
    }
}
