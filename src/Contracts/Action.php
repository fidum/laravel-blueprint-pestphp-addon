<?php

namespace Fidum\BlueprintPestAddon\Contracts;

use Blueprint\Tree;

interface Action
{
    /** @param \Illuminate\Contracts\Filesystem\Filesystem|\Illuminate\Filesystem\Filesystem $files */
    public function execute($files, Tree $tree): self;

    public function output(): array;
}
