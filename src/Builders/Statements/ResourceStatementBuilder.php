<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Statements\ResourceStatement;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;

class ResourceStatementBuilder extends StatementBuilder
{
    /** @var ResourceStatement */
    protected $statement;

    public function execute(): PendingOutput
    {
        return $this->output->addAssertion('response', '$response->assertOK();')
            ->addAssertion('response', '$response->assertJsonStructure([]);');
    }
}
