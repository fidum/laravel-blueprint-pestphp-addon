<?php

namespace Fidum\BlueprintPestAddon\Actions;

use Blueprint\Blueprint;
use Blueprint\Models\Controller;
use Fidum\BlueprintPestAddon\Builders\HttpTestBuilder;
use Fidum\BlueprintPestAddon\Contracts\Action;
use Fidum\BlueprintPestAddon\Traits\HasOutput;
use Fidum\BlueprintPestAddon\Traits\HasStubFile;
use Fidum\BlueprintPestAddon\Traits\PopulatesTestStub;

class MakeHttpTests implements Action
{
    use HasOutput;
    use HasStubFile;
    use PopulatesTestStub;

    public function execute($files, array $tree): Action
    {
        $stub = $this->stubFileContent('test.stub');

        $builder = new HttpTestBuilder($files, $tree);

        foreach ($tree['controllers'] as $controller) {
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

    private function getNamespace(Controller $controller)
    {
        return 'Tests\\Feature\\'.Blueprint::relativeNamespace($controller->fullyQualifiedNamespace());
    }

    private function getPath(Controller $controller)
    {
        $path = str_replace('\\', '/', Blueprint::relativeNamespace($controller->fullyQualifiedClassName()));

        return 'tests/Feature/'.$path.'Test.php';
    }
}
