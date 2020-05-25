<?php

namespace Fidum\BlueprintPestAddon\Actions;

use Fidum\BlueprintPestAddon\Contracts\Action;
use Fidum\BlueprintPestAddon\Traits\HasOutput;
use Fidum\BlueprintPestAddon\Traits\HasStubFile;
use Illuminate\Contracts\Filesystem\Filesystem;

class MakeControllerTests implements Action
{
    use HasOutput;
    use HasStubFile;

    public function execute(Filesystem $files, array $tree): Action
    {
        return $this;
    }
}
