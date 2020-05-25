<?php

namespace Fidum\BlueprintPestAddon\Contracts;

use Illuminate\Contracts\Filesystem\Filesystem;

interface Action
{
    public function execute(Filesystem $files, array $tree): Action;

    public function output(): array;
}
