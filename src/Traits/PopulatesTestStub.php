<?php

namespace Fidum\BlueprintPestAddon\Traits;

trait PopulatesTestStub
{
    private function populateTestStub(string $stub, string $namespace, string $testCases, string $imports = ''): string
    {
        $stub = str_replace('DummyNamespace', $namespace, $stub);
        $stub = str_replace("// imports...\n\n", $imports, $stub);
        $stub = str_replace("// imports...\r\n\r\n", $imports, $stub);
        return str_replace('// test cases...', $testCases, $stub);
    }

    private function populateTestCaseStub(string $stub, string $description, string $content)
    {
        $stub = str_replace('dummy_test_case', $description, $stub);
        return str_replace('// ...', $content, $stub);
    }
}
