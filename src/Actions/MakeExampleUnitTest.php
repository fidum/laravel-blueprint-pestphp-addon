<?php

namespace Fidum\BlueprintPestAddon\Actions;

use Fidum\BlueprintPestAddon\Contracts\Action;
use Fidum\BlueprintPestAddon\Traits\HasOutput;
use Fidum\BlueprintPestAddon\Traits\HasStubFile;
use Fidum\BlueprintPestAddon\Traits\PopulatesTestStub;

class MakeExampleUnitTest implements Action
{
    use HasOutput;
    use HasStubFile;
    use PopulatesTestStub;

    /** @var string */
    private $outputFilePath = 'tests/Unit/ExampleTest.php';

    public function execute($files, array $tree): Action
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
        return <<<'PEST'
it('is a basic unit test')
    ->assertTrue(true);
PEST;
    }
}
