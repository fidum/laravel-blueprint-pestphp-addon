<?php

namespace Fidum\BlueprintPestAddon;

trait HasStubFilePath
{
    protected function stubFilePath(string $fileName): string
    {
        return dirname(__DIR__).'/stubs/'.$fileName;
    }
}
