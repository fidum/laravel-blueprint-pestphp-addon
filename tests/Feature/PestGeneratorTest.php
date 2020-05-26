<?php

namespace Fidum\BlueprintPestAddon\Tests\Feature;


it('expects nothing to be generated when tree is empty', function () {
    /** @var FeatureTestCase $this */
    $this->files->expects('exists')->never();
    $this->files->expects('put')->never();

    $this->assertSame([], $this->subject->output([]));
});

it('expects nothing to be generated when no controllers were created', function () {
    /** @var FeatureTestCase $this */
    $this->files->expects('exists')->never();
    $this->files->expects('put')->never();

    $this->assertSame([], $this->subject->output(['controllers' => []]));
});

it('generates the expected output', function (
    string $definition,
    bool $pestGlobalFileExists,
    int $createdCount = 0,
    int $updatedCount = 0,
    bool $exampleFeature = false,
    bool $exampleUnit = false
) {
    /** @var FeatureTestCase $this */
    $tokens = $this->blueprint->parse($this->definition($definition));
    $tree = $this->blueprint->analyze($tokens);

    $exampleFileOutput = $this->getExampleTestsOutput($exampleFeature, $exampleUnit);
    $pestGlobalFileOutput = $this->getPestGlobalFileOutput($pestGlobalFileExists);
    $httpTestsOutput = $this->getHttpTestsOutput($tree);

    $expectedOutput = array_merge_recursive($pestGlobalFileOutput, $exampleFileOutput, $httpTestsOutput);

    $this->assertCount($createdCount, $expectedOutput['created'] ?? [], 'created count incorrect');
    $this->assertCount($updatedCount, $expectedOutput['updated'] ?? [], 'updated count incorrect');

    $this->assertSame($expectedOutput, $this->subject->output($tree));
})->with([
    'basic http tests' => ['example.yml', false, 2],
    'basic http test where pest global file exists' => ['example.yml', true, 1, 1],
    'basic http and example feature test files' => ['example.yml', true, 1, 2, true],
    'basic http and example unit test files' => ['example.yml', true, 1, 2, false, true],
    'basic http, example feature and unit test files' => ['example.yml', true, 1, 3, true, true],
]);
