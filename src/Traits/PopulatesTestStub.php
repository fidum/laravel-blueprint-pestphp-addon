<?php

namespace Fidum\BlueprintPestAddon\Traits;

trait PopulatesTestStub
{
    private function populateTestStub(string $stub, string $namespace, string $testCases, string $imports = ''): string
    {
        $stub = str_replace('DummyNamespace', $namespace, $stub);
        $stub = str_replace('// imports...', $imports, $stub);
        $stub = str_replace('// test cases...', $testCases, $stub);
        $stub = $this->removeSequentialBlankLines($stub);

        return $stub;
    }

    private function removeSequentialBlankLines(string $string): string
    {
        return preg_replace('/\r?\n(\s*\r?\n){2,}/', PHP_EOL . PHP_EOL, $string);
    }
}
