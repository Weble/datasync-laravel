<?php

namespace Weble\DataSyncLaravel\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeSyncRecipeCommand extends GeneratorCommand
{
    protected $name = 'datasync:recipe';
    protected $description = 'Create a new Sync Recipe class';
    protected $type = 'SyncRecipe';

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/recipe.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\SyncRecipes';
    }
}
