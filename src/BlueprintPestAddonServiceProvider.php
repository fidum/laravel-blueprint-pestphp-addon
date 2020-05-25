<?php

namespace Fidum\BlueprintPestAddon;

use Blueprint\Blueprint;
use Illuminate\Support\ServiceProvider;

class BlueprintPestAddonServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->extend(Blueprint::class, function (Blueprint $blueprint, $app) {
            $blueprint->swapGenerator(
                \Blueprint\Generators\TestGenerator::class,
                new PestGenerator($app['files'])
            );

            return $blueprint;
        });
    }
}
