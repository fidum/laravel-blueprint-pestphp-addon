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
        $assertion = $this->methodName === 'store'
            ? '$response->assertCreated();'
            : '$response->assertOK();';

        return $this->output->addAssertion('response', $assertion)
            ->addAssertion('response', '$response->assertJsonStructure([]);');
    }
}
