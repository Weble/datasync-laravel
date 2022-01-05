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
                    /** @var class-string $recipeClass */
                    $recipeClass = $namespace . str_replace(
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

                    if (in_array(SyncRecipeInterface::class, class_implements($recipeClass) ?: []) &&
                        ! (new \ReflectionClass($recipeClass))->isAbstract()) {
                        return $recipeClass;
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
