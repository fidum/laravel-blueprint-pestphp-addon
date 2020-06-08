<?php

namespace Fidum\BlueprintPestAddon\Contracts;

use Fidum\BlueprintPestAddon\Builders\PendingOutput;

interface StatementBuilder {

    public function execute(): PendingOutput;
}
