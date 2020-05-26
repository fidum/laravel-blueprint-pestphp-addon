<?php

namespace Fidum\BlueprintPestAddon\Traits;

trait PopulatesTestStub
{
    private function populateTestStub(string $stub, string $namespace, string $testCases, string $imports = ''): string
    {
        $stub = str_replace('DummyNamespace', $namespace, $stub);
        $stub = str_replace('// imports...', $imports, $stub);
        $stub = str_replace('// test cases...', $testCases, $stub);
        $stub = $this->cleanBlankLines($stub);

        return $stub;
    }

    private function cleanBlankLines(string $string): string
    {
        return preg_replace('/\n(\s*\n){2,}/', windows_os() ? "\r\n" : "\n\n", $string);
    }
}
