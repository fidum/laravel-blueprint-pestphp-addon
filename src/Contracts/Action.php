<?php

namespace Fidum\BlueprintPestAddon\Contracts;

interface Action
{
    /** @param \Illuminate\Contracts\Filesystem\Filesystem|\Illuminate\Filesystem\Filesystem $files */
    public function execute($files, array $tree): self;

    public function output(): array;
}
