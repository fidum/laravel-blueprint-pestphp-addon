<?php

namespace Fidum\BlueprintPestAddon\Tests\Feature;

use Blueprint\Blueprint;
use Blueprint\Lexers\ControllerLexer;
use Blueprint\Lexers\ModelLexer;
use Blueprint\Models\Controller;
use Fidum\BlueprintPestAddon\PestGenerator;
use Fidum\BlueprintPestAddon\Tests\TestCase;
use Illuminate\Contracts\Filesystem\Filesystem;
use Mockery\MockInterface;

class PestGeneratorTest extends TestCase
{
    /** @var Blueprint */
    public $blueprint;

    /** @var MockInterface */
    public $files;

    /** @var PestGenerator */
    public $subject;

    public $pestGlobalFile = 'tests/Pest.php';

    public $exampleFeatureFile = 'tests/Feature/ExampleTest.php';

    public $exampleUnitFile = 'tests/Unit/ExampleTest.php';

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = \Mockery::mock(Filesystem::class);

        $this->subject = new PestGenerator($this->files);

        $this->blueprint = new Blueprint();
        $this->blueprint->registerLexer(new ModelLexer());
        $this->blueprint->registerLexer(new ControllerLexer(new \Blueprint\Lexers\StatementLexer()));
        $this->blueprint->registerGenerator($this->subject);
    }

    public function testNothingGeneratedWithEmptyTree()
    {
        $this->files->expects('exists')->never();
        $this->files->expects('put')->never();

        /** @var Blueprint $blueprint */
        $this->assertSame([], $this->subject->output([]));
    }

    public function testNothingGeneratedWithEmptyControllersTree()
    {
        $this->files->expects('exists')->never();
        $this->files->expects('put')->never();

        /** @var Blueprint $blueprint */
        $this->assertSame([], $this->subject->output(['controllers' => []]));
    }

    /** @dataProvider provider */
    public function testGeneratedOutput(
        string $definition,
        bool $pestGlobalFileExists,
        bool $exampleFeature = false,
        bool $exampleUnit = false
    ) {
        /** @var Blueprint $blueprint */
        $tokens = $this->blueprint->parse($this->definition($definition));
        $tree = $this->blueprint->analyze($tokens);

        $exampleFileOutput = $this->getExampleTestsOutput($exampleFeature, $exampleUnit);
        $pestGlobalFileOutput = $this->getPestGlobalFileOutput($pestGlobalFileExists);
        $httpTestsOutput = $this->getHttpTestsOutput($tree);

        $this->assertSame(
            array_merge_recursive($pestGlobalFileOutput, $exampleFileOutput, $httpTestsOutput),
            $this->subject->output($tree)
        );
    }

    public function provider(): array
    {
        return [
            'basic http tests' => ['example.yml', false],
            'basic http test where pest global file exists' => ['example.yml', true],
            'basic http and example feature test files' => ['example.yml', true, true],
            'basic http, example feature and unit test files' => ['example.yml', true, false, true],
        ];
    }

    private function getExampleTestsOutput(bool $featureExists, bool $unitExists): array
    {
        $this->files->expects('exists')->with($this->exampleFeatureFile)->andReturn($featureExists);
        $this->files->expects('exists')->with($this->exampleUnitFile)->andReturn($unitExists);

        $output = [];

        if ($featureExists) {
            $this->files->expects('put')->with($this->exampleFeatureFile, $this->fixture($this->exampleFeatureFile));
            $output['updated'][] = $this->exampleFeatureFile;
        }

        if ($unitExists) {
            $this->files->expects('put')->with($this->exampleUnitFile, $this->fixture($this->exampleUnitFile));
            $output['updated'][] = $this->exampleUnitFile;
        }

        return $output;
    }

    private function getHttpTestsOutput(array $tree): array
    {
        $this->files->expects('exists')->with('tests/Feature/Http/Controllers')->andReturnTrue();

        $output = [];

        /** @var Controller $controller */
        foreach ($tree['controllers'] as $controller) {
            $ns = str_replace('\\', '/', Blueprint::relativeNamespace($controller->fullyQualifiedClassName()));
            $path = 'tests/Feature/'.$ns.'Test.php';
            $this->files->expects('put')->with($path, $this->fixture($path));
            $output['created'][] = $path;
        }

        return $output;
    }

    private function getPestGlobalFileOutput(bool $updated)
    {
        $this->files->expects('exists')->with($this->pestGlobalFile)->andReturn($updated);
        $this->files->expects('put')->with($this->pestGlobalFile, $this->fixture($this->pestGlobalFile));

        $key = $updated ? 'updated' : 'created';

        return [$key => [$this->pestGlobalFile]];
    }
}
