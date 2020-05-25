<?php

namespace Fidum\BlueprintPestAddon\Contracts;

use Illuminate\Contracts\Filesystem\Filesystem;

interface Action
{
    public function execute(Filesystem $files, array $tree): self;

    public function output(): array;
}
