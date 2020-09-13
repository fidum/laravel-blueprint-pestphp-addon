<?php

namespace Fidum\BlueprintPestAddon\Actions;

use Blueprint\Tree;
use Fidum\BlueprintPestAddon\Concerns\TracksFileOutput;
use Fidum\BlueprintPestAddon\Concerns\ReadsStubFiles;
use Fidum\BlueprintPestAddon\Contracts\Action;

class MakePestGlobalFile implements Action
{
    use ReadsStubFiles;
    use TracksFileOutput;

    /** @var string */
    private $outputFilePath = 'tests/Pest.php';

    public function execute($files, Tree $tree): Action
    {
        $updated = $files->exists($this->outputFilePath);

        $content = $this->stubFileContent('pest.stub');

        $files->put($this->outputFilePath, $content);

        $this->addOutput($this->outputFilePath, $updated);

        return $this;
    }
}
