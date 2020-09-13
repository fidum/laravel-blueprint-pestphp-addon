<?php

namespace Fidum\BlueprintPestAddon\Actions;

use Blueprint\Blueprint;
use Blueprint\Models\Controller;
use Blueprint\Tree;
use Fidum\BlueprintPestAddon\Builders\HttpTestBuilder;
use Fidum\BlueprintPestAddon\Contracts\Action;
use Fidum\BlueprintPestAddon\Concerns\HasOutput;
use Fidum\BlueprintPestAddon\Concerns\HasStubFile;
use Fidum\BlueprintPestAddon\Concerns\PopulatesTestStub;

class MakeHttpTests implements Action
{
    use HasOutput;
    use HasStubFile;
    use PopulatesTestStub;

    public function execute($files, Tree $tree): Action
    {
        $stub = $this->stubFileContent('test.stub');

        $builder = new HttpTestBuilder($files, $tree);

        foreach ($tree->controllers() as $controller) {
            $path = $this->getPath($controller);

            if (! $files->exists(dirname($path))) {
                $files->makeDirectory(dirname($path), 0755, true);
            }

            $files->put($path, $this->populateTestStub(
                $stub,
                $this->getNamespace($controller),
                $builder->testCases($controller),
                $builder->imports($controller)
            ));

            $this->created($path);
        }

        return $this;
    }

    private function getNamespace(Controller $controller): string
    {
        return 'Tests\\Feature\\'.Blueprint::relativeNamespace($controller->fullyQualifiedNamespace());
    }

    private function getPath(Controller $controller): string
    {
        $path = str_replace('\\', '/', Blueprint::relativeNamespace($controller->fullyQualifiedClassName()));

        return 'tests/Feature/'.$path.'Test.php';
    }
}
