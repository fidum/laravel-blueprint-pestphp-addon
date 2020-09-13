<?php

namespace Fidum\BlueprintPestAddon\Concerns;

trait HasStubFile
{
    protected function stubFilePath(string $fileName): string
    {
        return dirname(__DIR__, 2).'/stubs/'.$fileName;
    }

    protected function stubFileContent(string $fileName): string
    {
        $path = $this->stubFilePath($fileName);

        return file_get_contents($path);
    }
}
