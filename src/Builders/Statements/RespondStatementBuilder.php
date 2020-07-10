<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Statements\RespondStatement;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;
use Fidum\BlueprintPestAddon\Enums\Coverage;

class RespondStatementBuilder extends StatementBuilder
{
    /** @var RespondStatement */
    protected $statement;

    public function execute(): PendingOutput
    {
        $this->output->addCoverage(Coverage::RESPONDS);

        if ($this->statement->content()) {
            $this->output->addAssertion(
                'response',
                '$response->assertJson($'.($this->statement->content() ?? '').');',
                true
            );
        }

        if ($this->statement->status() === 200) {
            return $this->output->addAssertion('response', '$response->assertOk();', true);
        }

        if ($this->statement->status() === 204) {
            return $this->output->addAssertion('response', '$response->assertNoContent();', true);
        }

        return $this->output
            ->addAssertion('response', '$response->assertNoContent('.$this->statement->status().');', true);
    }
}
