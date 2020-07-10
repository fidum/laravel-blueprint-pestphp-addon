<?php

namespace Fidum\BlueprintPestAddon;

use Blueprint\Blueprint;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class BlueprintPestAddonServiceProvider extends ServiceProvider
{
    public function register()
    {
        /** @param array $app */
        $this->app->extend(Blueprint::class, function (Blueprint $blueprint, Container $app) {
            $blueprint->swapGenerator(
                \Blueprint\Generators\TestGenerator::class,
                new PestGenerator($app['files'])
            );

            return $blueprint;
        });
    }
}
