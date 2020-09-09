<?php

namespace Fidum\BlueprintPestAddon\Builders\Concerns;

trait ImportsGlobalFunctions
{
    public function addPHPUnitGlobalFunctionImport(string $functionName): void
    {
        $this->output->addImport("function PHPUnit\\Framework\\$functionName");
    }
}
