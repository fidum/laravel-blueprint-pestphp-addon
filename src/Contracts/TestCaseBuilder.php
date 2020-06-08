<?php

namespace Fidum\BlueprintPestAddon\Contracts;

interface TestCaseBuilder
{
    public function testCase(string $stub): string;
}
