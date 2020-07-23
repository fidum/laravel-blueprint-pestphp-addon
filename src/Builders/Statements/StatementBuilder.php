<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Controller;
use Blueprint\Tree;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;
use Fidum\BlueprintPestAddon\Contracts\StatementBuilder as StatementBuilderContract;
use Illuminate\Support\Str;

abstract class StatementBuilder implements StatementBuilderContract
{
    /** @var string */
    protected $context;

    /** @var Controller */
    protected $controller;

    /** @var string */
    protected $methodName;

    /** @var PendingOutput */
    protected $output;

    /** @var object */
    protected $statement;

    /** @var Tree */
    protected $tree;

    /** @var string */
    protected $variable;

    public function __construct(
        Controller $controller,
        string $methodName,
        object $statement,
        PendingOutput $output,
        Tree $tree
    ) {
        $this->controller = $controller;
        $this->methodName = $methodName;
        $this->output = $output;
        $this->statement = $statement;
        $this->tree = $tree;
        $this->context = PendingOutput::context($this->controller);
        $this->variable = Str::camel($this->context);
    }
}
