<?php

namespace Fidum\BlueprintPestAddon\Actions;

use Fidum\BlueprintPestAddon\Contracts\Action;
use Fidum\BlueprintPestAddon\Traits\HasOutput;
use Fidum\BlueprintPestAddon\Traits\HasStubFile;

class MakePestGlobalFile implements Action
{
    use HasOutput;
    use HasStubFile;

    /** @var string */
    private $outputFilePath = 'tests/Pest.php';

    public function execute($files, array $tree): Action
    {
        $updated = $files->exists($this->outputFilePath);

        $content = $this->stubFileContent('pest.stub');

        $files->put($this->outputFilePath, $content);

        $this->addOutput($this->outputFilePath, $updated);

        return $this;
    }
}
