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

it('generates the expected output', pestGeneratorTest('8.0.0'))->with('pest');

it('legacy: generates the expected output', pestGeneratorTest('7.0.0', true))->with('pest');

function pestGeneratorTest($version, bool $legacy = false): callable {
    return function (
        string $definition,
        bool $pestGlobalFileExists,
        int $createdCount = 0,
        int $updatedCount = 0,
        array $config = [],
        bool $exampleFeature = false,
        bool $exampleUnit = false,
        string $folder = ''
    ) use ($version, $legacy) {
        /** @var FeatureTestCase $this */
        $this->useLaravelVersion($version);

        $defaultConfig = $this->app['config']->get('blueprint');
        $this->app['config']->set('blueprint', array_merge($defaultConfig, $config));

        $tokens = $this->blueprint->parse($this->definition($definition));
        $tree = $this->blueprint->analyze($tokens);

        $exampleFileOutput = $this->getExampleTestsOutput($exampleFeature, $exampleUnit);
        $pestGlobalFileOutput = $this->getPestGlobalFileOutput($pestGlobalFileExists);
        $httpTestsOutput = $this->getHttpTestsOutput($tree,
            'tests/Feature/Http/Controllers'. ($folder ? '/'.$folder : ''),
            $legacy ? 'tests/Feature/Http/Legacy/'.$folder : null,
        );

        $expectedOutput = array_merge_recursive($pestGlobalFileOutput, $exampleFileOutput, $httpTestsOutput);

        expect($expectedOutput['created'] ?? [])->toHaveCount($createdCount);
        expect($expectedOutput['updated'] ?? [])->toHaveCount($updatedCount);
        expect($this->subject->output($tree))->toBe($expectedOutput);
    };
}
