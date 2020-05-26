<?php

namespace Fidum\BlueprintPestAddon\Actions;

use Fidum\BlueprintPestAddon\Contracts\Action;
use Fidum\BlueprintPestAddon\Traits\PopulatesTestStub;
use Fidum\BlueprintPestAddon\Traits\HasOutput;
use Fidum\BlueprintPestAddon\Traits\HasStubFile;

class MakeExampleFeatureTest implements Action
{
    use HasOutput;
    use HasStubFile;
    use PopulatesTestStub;

    /** @var string */
    private $outputFilePath = 'tests/Feature/ExampleTest.php';

    public function execute($files, array $tree): Action
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
        return <<<PEST
it('is a basic feature test')
    ->get('/')
    ->assertStatus(200);
PEST;

    }
}
