<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Controller;
use Blueprint\Models\Statements\SessionStatement;
use Blueprint\Tree;
use Fidum\BlueprintPestAddon\Builders\Concerns\DeterminesModels;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;

class InitialStatementBuilder extends StatementBuilder
{
    use DeterminesModels;

    public function __construct(
        protected Controller $controller,
        protected string $methodName,
        protected PendingOutput $output,
        protected Tree $tree
    ) {
        parent::__construct($controller, $methodName, new SessionStatement('', ''), $output, $tree);
    }

    public function execute(): PendingOutput
    {
        if (in_array($this->methodName, ['edit', 'update', 'show', 'destroy'])) {
            $model = $this->controller->prefix();

            $this->output
                ->addFactory($this->variable, $model)
                ->addImport($this->fullyQualifiedModelClassName($model));
        }

        return $this->output;
    }
}
