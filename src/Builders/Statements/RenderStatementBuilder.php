<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Statements\RenderStatement;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;
use Fidum\BlueprintPestAddon\Enums\Coverage;

class RenderStatementBuilder extends StatementBuilder
{
    /** @var RenderStatement */
    protected object $statement;

    public function execute(): PendingOutput
    {
        $this->output->addCoverage(Coverage::VIEW);

        $viewAssertions = [];
        $viewAssertions[] = '$response->assertOk();';
        $viewAssertions[] = sprintf('$response->assertViewIs(\'%s\');', $this->statement->view());

        foreach ($this->statement->data() as $data) {
            $viewAssertions[] = sprintf('$response->assertViewHas(\'%s\');', $data);
        }

        return $this->output->addAssertions('response', $viewAssertions);
    }
}
