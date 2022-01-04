<?php

namespace Weble\DataSyncLaravel\Support;

use Illuminate\Support\Str;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Weble\DataSync\Contracts\SyncRecipeInterface;

class DiscoverSyncRecipes
{
    public static function within(array $directories): array
    {
        $namespace = app()->getNamespace();

        try {
            return collect((new Finder())->in($directories)->files())
                ->map(function ($recipe) use ($namespace) {
                    $recipe = $namespace . str_replace(
                        [
                                '/',
                                '.php',
                            ],
                        [
                                '\\',
                                '',
                            ],
                        Str::after($recipe->getRealPath(), realpath(app_path()) . DIRECTORY_SEPARATOR)
                    );

                    if (class_implements($recipe, SyncRecipeInterface::class) &&
                        ! (new \ReflectionClass($recipe))->isAbstract()) {
                        return $recipe;
                    }

                    return null;
                })
                ->filter()
                ->values()
                ->toArray();
        } catch (DirectoryNotFoundException $e) {
            return [];
        }
    }
}
