<?php

namespace Fidum\BlueprintPestAddon\Actions;

use Blueprint\Tree;
use Fidum\BlueprintPestAddon\Concerns\TracksFileOutput;
use Fidum\BlueprintPestAddon\Concerns\ReadsStubFiles;
use Fidum\BlueprintPestAddon\Concerns\PopulatesTestStub;
use Fidum\BlueprintPestAddon\Contracts\Action;

class MakeExampleUnitTest implements Action
{
    use PopulatesTestStub;
    use ReadsStubFiles;
    use TracksFileOutput;

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
