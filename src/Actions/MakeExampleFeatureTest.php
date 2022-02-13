<?php

namespace Fidum\BlueprintPestAddon\Actions;

use Blueprint\Tree;
use Fidum\BlueprintPestAddon\Concerns\PopulatesTestStub;
use Fidum\BlueprintPestAddon\Concerns\ReadsStubFiles;
use Fidum\BlueprintPestAddon\Concerns\TracksFileOutput;
use Fidum\BlueprintPestAddon\Contracts\Action;

class MakeExampleFeatureTest implements Action
{
    use PopulatesTestStub;
    use ReadsStubFiles;
    use TracksFileOutput;

    private string $outputFilePath = 'tests/Feature/ExampleTest.php';

    public function execute($files, Tree $tree): Action
    {
        if ($files->exists($this->outputFilePath)) {
            $stub = $this->stubFileContent('test.stub');

            $testCase = $this->populateTestStub($stub, 'Tests\\Feature', $this->testCase());

            $files->put($this->outputFilePath, $testCase);

            $this->updated($this->outputFilePath);
        }

        return $this;
    }

    private function testCase(): string
    {
        $stub = $this->stubFileContent('case_hoc.stub');

        $content = "->get('/')".PHP_EOL.str_pad('', 4, ' ', STR_PAD_LEFT).'->assertStatus(200)';

        return $this->populateTestCaseStub($stub, 'is a basic feature test', $content);
    }
}
