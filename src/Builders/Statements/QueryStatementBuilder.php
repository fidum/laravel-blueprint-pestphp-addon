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
            ->addSetUp('data', sprintf(
                '$%s = factory(%s::class, 3)->create();',
                Str::plural($this->variable),
                $model
            ))
            ->addImport($this->modelNamespace().'\\'.$this->determineModel($model, $this->statement->model()));
    }
}
