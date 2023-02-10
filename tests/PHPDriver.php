<?php

namespace Fidum\BlueprintPestAddon\Tests;

use Spatie\Snapshots\Drivers\TextDriver;

class PHPDriver extends TextDriver
{
    public function extension(): string
    {
        return 'php';
    }
}
