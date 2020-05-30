<?php

namespace Fidum\BlueprintPestAddon\Tests\Feature;

it('expects nothing to be generated when tree is empty', function () {
    /** @var FeatureTestCase $this */
    $this->files->expects('exists')->never();
    $this->files->expects('put')->never();

    assertSame([], $this->subject->output([]));
});

it('expects nothing to be generated when no controllers were created', function () {
    /** @var FeatureTestCase $this */
    $this->files->expects('exists')->never();
    $this->files->expects('put')->never();

    assertSame([], $this->subject->output(['controllers' => []]));
});

it('generates the expected output', function (
    string $definition,
    bool $pestGlobalFileExists,
    int $createdCount = 0,
    int $updatedCount = 0,
    bool $exampleFeature = false,
    bool $exampleUnit = false,
    string $controllerPath = 'tests/Feature/Http/Controllers'
) {
    /** @var FeatureTestCase $this */
    $tokens = $this->blueprint->parse($this->definition($definition));
    $tree = $this->blueprint->analyze($tokens);

    $exampleFileOutput = $this->getExampleTestsOutput($exampleFeature, $exampleUnit);
    $pestGlobalFileOutput = $this->getPestGlobalFileOutput($pestGlobalFileExists);
    $httpTestsOutput = $this->getHttpTestsOutput($tree, $controllerPath);

    $expectedOutput = array_merge_recursive($pestGlobalFileOutput, $exampleFileOutput, $httpTestsOutput);

    assertCount($createdCount, $expectedOutput['created'] ?? [], 'created count incorrect');
    assertCount($updatedCount, $expectedOutput['updated'] ?? [], 'updated count incorrect');
    assertSame($expectedOutput, $this->subject->output($tree));
})->with([
    'basic http tests' => ['simple.yml', false, 2],
    'basic http test where pest global file exists' => ['simple.yml', true, 1, 1],
    'basic http and example feature test files' => ['simple.yml', true, 1, 2, true],
    'basic http and example unit test files' => ['simple.yml', true, 1, 2, false, true],
    'basic http, example feature and unit test files' => ['simple.yml', true, 1, 3, true, true],
    'multiple crud resources created with additional facades' => ['crud.yml', false, 3],
    'api resource controller test created' => ['api.yml', false, 2],
    'custom queries defined on controller routes' => ['query.yml', false, 2],
    'controller test created in specified subfolder' => ['subfolder.yml', false, 2, 0, false, false, 'tests/Feature/Http/Controllers/Api'],
]);
