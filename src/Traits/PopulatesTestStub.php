<?php

namespace Fidum\BlueprintPestAddon\Traits;

trait PopulatesTestStub
{
    private function populateTestStub(string $stub, string $namespace, string $testCases, string $imports = ''): string
    {
        $stub = str_replace('DummyNamespace', $namespace, $stub);
        $stub = str_replace("// imports...\n\n", $imports, $stub);
        $stub = str_replace("// imports...\r\n\r\n", $imports, $stub);
        $stub = str_replace('// test cases...', $testCases, $stub);

        return $stub;
    }
}
