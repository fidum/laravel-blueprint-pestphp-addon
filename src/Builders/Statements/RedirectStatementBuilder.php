<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Statements\RedirectStatement;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;
use Fidum\BlueprintPestAddon\Enums\Coverage;
use Illuminate\Support\Str;

class RedirectStatementBuilder extends StatementBuilder
{
    /** @var RedirectStatement */
    protected object $statement;

    public function execute(): PendingOutput
    {
        $this->output->addCoverage(Coverage::REDIRECT);

        $assertion = sprintf('$response->assertRedirect(route(\'%s\'', $this->statement->route());

        if ($this->statement->data()) {
            $parameters = array_map(function ($parameter) {
                return '$'.$parameter;
            }, $this->statement->data());

            $assertion .= ', ['.implode(', ', $parameters).']';
        } elseif (Str::contains($this->statement->route(), '.')) {
            [$model, $action] = explode('.', $this->statement->route());
            if (in_array($action, ['edit', 'update', 'show', 'destroy'])) {
                $assertion .= sprintf(", ['%s' => $%s]", $model, $model);
            }
        }

        $assertion .= '));';

        return $this->output->addAssertion('response', $assertion, true);
    }
}
