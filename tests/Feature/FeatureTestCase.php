<?php

namespace Fidum\BlueprintPestAddon\Tests\Feature;

use Blueprint\Blueprint;
use Blueprint\Lexers\ControllerLexer;
use Blueprint\Lexers\ModelLexer;
use Blueprint\Lexers\StatementLexer;
use Blueprint\Models\Controller;
use Blueprint\Tree;
use Fidum\BlueprintPestAddon\PestGenerator;
use Fidum\BlueprintPestAddon\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Mockery\MockInterface;

class FeatureTestCase extends TestCase
{
    protected Blueprint $blueprint;

    protected MockInterface $files;

    protected PestGenerator $subject;

    protected string $pestGlobalFile = 'tests/Pest.php';

    protected string $exampleFeatureFile = 'tests/Feature/ExampleTest.php';

    protected string $exampleUnitFile = 'tests/Unit/ExampleTest.php';

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = \Mockery::mock(Filesystem::class);

        $this->subject = new PestGenerator($this->files);

        $this->blueprint = new Blueprint();
        $this->blueprint->registerLexer(new ModelLexer());
        $this->blueprint->registerLexer(new ControllerLexer(new StatementLexer()));
        $this->blueprint->registerGenerator($this->subject);
    }

    protected function definition(string $fileName = 'simple.yml'): string
    {
        return $this->fixture('definitions'.DIRECTORY_SEPARATOR.$fileName);
    }

    protected function fixture(string $path): string
    {
        return file_get_contents(
            dirname(__DIR__, 1).DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR)
        );
    }

    protected function getExampleTestsOutput(bool $featureExists, bool $unitExists): array
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

    protected function getHttpTestsOutput(Tree $tree, string $controllerPath): array
    {
        $controllers = $tree->controllers();

        $this->files->expects('exists')
            ->times(count($controllers))
            ->withArgs(function (string $path) use ($controllerPath) {
                $this->assertSame($controllerPath, $path);

                return true;
            })
            ->andReturnFalse();

        $this->files->expects('makeDirectory')
            ->times(count($controllers))
            ->with($controllerPath, 0755, true);

        $output = [];

        /** @var Controller $controller */
        foreach ($controllers as $controller) {
            $ns = str_replace('\\', '/', Blueprint::relativeNamespace($controller->fullyQualifiedClassName()));
            $controllerPath = 'tests/Feature/'.$ns.'Test.php';

            $this->files->expects('put')
                ->withArgs(function ($pathArg, $output) use ($controllerPath) {
                    $this->assertSame($controllerPath, $pathArg);
                    $this->assertSame($this->fixture($controllerPath), $output);

                    return true;
                });

            $output['created'][] = $controllerPath;
        }

        return $output;
    }

    protected function getPestGlobalFileOutput(bool $updated): array
    {
        $this->files->expects('exists')->with($this->pestGlobalFile)->andReturn($updated);
        $this->files->expects('put')->with($this->pestGlobalFile, $this->fixture($this->pestGlobalFile));

        $key = $updated ? 'updated' : 'created';

        return [$key => [$this->pestGlobalFile]];
    }
}
