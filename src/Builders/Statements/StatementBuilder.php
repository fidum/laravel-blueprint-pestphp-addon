<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Controller;
use Blueprint\Tree;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;
use Fidum\BlueprintPestAddon\Contracts\StatementBuilder as StatementBuilderContract;
use Illuminate\Support\Str;

abstract class StatementBuilder implements StatementBuilderContract
{
    protected string $context;

    protected string $variable;

    public function __construct(
        protected Controller $controller,
        protected string $methodName,
        protected object $statement,
        protected PendingOutput $output,
        protected Tree $tree
    ) {
        $this->context = PendingOutput::context($this->controller);
        $this->variable = Str::camel($this->context);
    }
}
