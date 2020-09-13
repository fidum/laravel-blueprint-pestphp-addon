<?php

namespace Fidum\BlueprintPestAddon\Actions;

use Blueprint\Tree;
use Fidum\BlueprintPestAddon\Contracts\Action;
use Fidum\BlueprintPestAddon\Concerns\HasOutput;
use Fidum\BlueprintPestAddon\Concerns\HasStubFile;
use Fidum\BlueprintPestAddon\Concerns\PopulatesTestStub;

class MakeExampleUnitTest implements Action
{
    use HasOutput;
    use HasStubFile;
    use PopulatesTestStub;

    /** @var string */
    private $outputFilePath = 'tests/Unit/ExampleTest.php';

    public function execute($files, Tree $tree): Action
    {
        if ($files->exists($this->outputFilePath)) {
            $stub = $this->stubFileContent('test.stub');

            $testCase = $this->populateTestStub($stub, 'Tests\\Unit', $this->testCase());

            $files->put($this->outputFilePath, $testCase);

            $this->updated($this->outputFilePath);
        }

        return $this;
    }

    private function testCase(): string
    {
        $stub = $this->stubFileContent('case_hoc.stub');

        return $this->populateTestCaseStub($stub, 'is a basic unit test', '->assertTrue(true)');
    }
}
