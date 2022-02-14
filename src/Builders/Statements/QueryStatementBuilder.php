<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Statements\QueryStatement;
use Fidum\BlueprintPestAddon\Builders\Concerns\DeterminesModels;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;
use Illuminate\Support\Str;

class QueryStatementBuilder extends StatementBuilder
{
    use DeterminesModels;

    /** @var QueryStatement */
    protected object $statement;

    public function execute(): PendingOutput
    {
        $model = $this->controller->prefix();

        return $this->output
            ->addFactory(Str::plural($this->variable), $model, 3)
            ->addImport($this->fullyQualifiedModelClassName($model));
    }
}
