<?php

namespace Fidum\BlueprintPestAddon\Actions;

use Blueprint\Tree;
use Fidum\BlueprintPestAddon\Contracts\Action;
use Fidum\BlueprintPestAddon\Concerns\HasOutput;
use Fidum\BlueprintPestAddon\Concerns\HasStubFile;

class MakePestGlobalFile implements Action
{
    use HasOutput;
    use HasStubFile;

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
