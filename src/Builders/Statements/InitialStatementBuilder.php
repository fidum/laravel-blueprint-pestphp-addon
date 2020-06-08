<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Controller;
use Blueprint\Models\Statements\SessionStatement;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;

class InitialStatementBuilder extends StatementBuilder
{
    public function __construct(
        Controller $controller,
        string $methodName,
        PendingOutput $output,
        array $models = []
    ) {
        parent::__construct($controller, $methodName, new SessionStatement('', ''), $output, $models);
    }

    public function execute(): PendingOutput
    {
        if (in_array($this->methodName, ['edit', 'update', 'show', 'destroy'])) {
            $model = $this->controller->prefix();

            $this->output->addSetUp('data', sprintf('$%s = factory(%s::class)->create();', $this->variable, $model));
        }

        return $this->output;
    }
}
