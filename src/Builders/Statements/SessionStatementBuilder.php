<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Statements\SessionStatement;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;

class SessionStatementBuilder extends StatementBuilder
{
    /** @var SessionStatement */
    protected object $statement;

    public function execute(): PendingOutput
    {
        return $this->output->addAssertion('response', sprintf(
            '$response->assertSessionHas(\'%s\', %s);',
            $this->statement->reference(),
            '$'.str_replace('.', '->', $this->statement->reference())
        ));
    }
}
