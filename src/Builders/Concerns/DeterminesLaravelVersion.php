<?php

namespace Fidum\BlueprintPestAddon\Builders\Concerns;

use Illuminate\Support\Facades\App;

trait DeterminesLaravelVersion
{
    private static function isLaravel8OrHigher()
    {
        return version_compare(App::version(), '8.0.0', '>=');
    }
}
