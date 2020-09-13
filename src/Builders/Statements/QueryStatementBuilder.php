<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Statements\QueryStatement;
use Fidum\BlueprintPestAddon\Builders\Concerns\ModelStatementHelper;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;
use Illuminate\Support\Str;

class QueryStatementBuilder extends StatementBuilder
{
    use ModelStatementHelper;

    /** @var QueryStatement */
    protected $statement;

    public function execute(): PendingOutput
    {
        $model = $this->controller->prefix();

        return $this->output
            ->addFactory(Str::plural($this->variable), $model, 3)
            ->addImport($this->modelNamespace().'\\'.$this->determineModel($model, $this->statement->model()));
    }
}
