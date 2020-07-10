<?php

namespace Fidum\BlueprintPestAddon\Tests;

use Blueprint\BlueprintServiceProvider;
use Fidum\BlueprintPestAddon\BlueprintPestAddonServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        // blueprint config
        $app['config']->set('blueprint.namespace', 'App');
        $app['config']->set('blueprint.models_namespace', '');
        $app['config']->set('blueprint.app_path', 'app');
    }

    protected function getPackageProviders($app): array
    {
        return [
            BlueprintServiceProvider::class,
            BlueprintPestAddonServiceProvider::class,
        ];
    }
}
