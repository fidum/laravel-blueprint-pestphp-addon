<?php

namespace Fidum\BlueprintPestAddon;

use Blueprint\Blueprint;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class BlueprintPestAddonServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->app->extend(Blueprint::class, function (Blueprint $blueprint, $app) {
            $blueprint->swapGenerator(
                \Blueprint\Generators\TestGenerator::class,
                new PestGenerator($app['files'])
            );

            return $blueprint;
        });
    }

    public function provides()
    {
        return [
            Blueprint::class,
        ];
    }
}
