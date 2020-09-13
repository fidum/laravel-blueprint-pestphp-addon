<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Controller;
use Blueprint\Models\Statements\SessionStatement;
use Blueprint\Tree;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;

class InitialStatementBuilder extends StatementBuilder
{
    public function __construct(
        Controller $controller,
        string $methodName,
        PendingOutput $output,
        Tree $tree
    ) {
        parent::__construct($controller, $methodName, new SessionStatement('', ''), $output, $tree);
    }

    public function execute(): PendingOutput
    {
        if (in_array($this->methodName, ['edit', 'update', 'show', 'destroy'])) {
            $model = $this->controller->prefix();

            $this->output->addFactory($this->variable, $model);
        }

        return $this->output;
    }
}
