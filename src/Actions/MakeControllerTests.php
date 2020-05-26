<?php

namespace Fidum\BlueprintPestAddon\Actions;

use Blueprint\Blueprint;
use Blueprint\Models\Controller;
use Fidum\BlueprintPestAddon\Contracts\Action;
use Fidum\BlueprintPestAddon\Traits\HasOutput;
use Fidum\BlueprintPestAddon\Traits\HasStubFile;
use Fidum\BlueprintPestAddon\Traits\PopulatesTestStub;

class MakeControllerTests implements Action
{
    use HasOutput;
    use HasStubFile;
    use PopulatesTestStub;

    private $models = [];

    private $imports = [];

    public function execute($files, array $tree): Action
    {
        $this->registerModels($tree);

        $stub = $this->stubFileContent('test.stub');

        foreach ($tree['controllers'] as $controller) {
            $path = $this->getPath($controller);

            if (!$files->exists(dirname($path))) {
                $files->makeDirectory(dirname($path), 0755, true);
            }

            $files->put($path, $this->populateTestStub(
                $stub,
                $this->getNamespace($controller),
                $this->buildTestCases($controller),
                $this->buildImports($controller)
            ));

            $this->created($path);
        }

        return $this;
    }

    private function addImport(Controller $controller, $class)
    {
        $this->imports[$controller->name()][] = $class;
    }

    private function buildImports(Controller $controller)
    {
        $imports = array_unique($this->imports[$controller->name()] ?? []);
        sort($imports);

        return implode(PHP_EOL, array_map(function ($class) {
            return 'use ' . $class . ';';
        }, $imports));
    }

    private function buildTestCases(Controller $controller)
    {
        return <<<'PEST'
it('this is an example test')
    ->assertTrue(true);
PEST;
    }

    private function getNamespace(Controller $controller)
    {
        return 'Tests\\Feature\\' . Blueprint::relativeNamespace($controller->fullyQualifiedNamespace());
    }

    private function getPath(Controller $controller)
    {
        // TODO: Use Blueprints TestGenerator?
        $path = str_replace('\\', '/', Blueprint::relativeNamespace($controller->fullyQualifiedClassName()));

        return 'tests/Feature/' . $path . 'Test.php';
    }

    private function registerModels(array $tree)
    {
        $this->models = array_merge($tree['cache'] ?? [], $tree['models'] ?? []);
    }
}
