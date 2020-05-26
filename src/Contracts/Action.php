<?php

namespace Fidum\BlueprintPestAddon\Contracts;

use Illuminate\Contracts\Filesystem\Filesystem;

interface Action
{
    /** @param Filesystem $files */
    public function execute($files, array $tree): self;

    public function output(): array;
}
