<?php

namespace Fidum\BlueprintPestAddon\Tests\Feature;

use Blueprint\Tree;

it('should return expected types', function () {
    /** @var FeatureTestCase $this */
    expect($this->subject->types())->toBe(['controllers', 'tests']);
});

it('expects nothing to be generated when no controllers were created', function () {
    /** @var FeatureTestCase $this */
    $this->files->expects('exists')->never();
    $this->files->expects('put')->never();
    $files = $this->subject->output(new Tree(['controllers' => []]));

    expect($files)->toBeArray()->toBeEmpty();
});

it('generates tests using class factories', function (
    string $definition,
    bool $pestGlobalFileExists,
    int $createdCount = 0,
    int $updatedCount = 0,
    array $config = [],
    bool $exampleFeature = false,
    bool $exampleUnit = false,
    string $folder = ''
) {
    /** @var FeatureTestCase $this */
    $defaultConfig = $this->app['config']->get('blueprint');
    $this->app['config']->set('blueprint', array_merge($defaultConfig, $config));

    $tokens = $this->blueprint->parse($this->definition($definition));
    $tree = $this->blueprint->analyze($tokens);

    $pestGlobalFileOutput = $this->getPestGlobalFileOutput($pestGlobalFileExists);
    $exampleFileOutput = $this->getExampleTestsOutput($exampleFeature, $exampleUnit);
    $httpTestsOutput = $this->getHttpTestsOutput(
        $tree,
        'tests/Feature/Http/Controllers'.($folder ? '/'.$folder : ''),
    );

    $expectedOutput = array_merge_recursive($pestGlobalFileOutput, $exampleFileOutput, $httpTestsOutput);

    expect($expectedOutput['created'] ?? [])->toHaveCount($createdCount)
        ->and($expectedOutput['updated'] ?? [])->toHaveCount($updatedCount)
        ->and($this->subject->output($tree))->toBe($expectedOutput);
})->with('pest');
