<?php

namespace Fidum\BlueprintPestAddon\Tests\Datasets;

dataset('pest', [
    'basic http tests' => ['simple.yml', false, 2],
    'basic http test where pest global file exists' => ['simple.yml', true, 1, 1],
    'basic http and example feature test files' => ['simple.yml', true, 1, 2, [], true],
    'basic http and example unit test files' => ['simple.yml', true, 1, 2, [], false, true],
    'basic http, example feature and unit test files' => ['simple.yml', true, 1, 3, [], true, true],
    'multiple crud resources created with additional facades' => ['crud.yml', false, 3],
    'invokable with dispatch' => ['invokable.yml', false, 3],
    'controller test created with nested models' => ['nested.yml', false, 2],
    'api resource controller test created with custom model namespace' => ['api.yml', false, 2, 0, ['models_namespace' => 'Models']],
    'custom queries defined on controller routes' => ['query.yml', false, 2],
    'controller test created in specified subfolder' => ['subfolder.yml', false, 2, 0, [], false, false, 'Api'],
    'controller test created in specified subfolder with nested models' => ['subfolder_nested.yml', false, 2, 0, ['models_namespace' => 'Models'], false, false, 'Screening'],
]);
