<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Controller;
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

    /** @var array */
    protected $models;

    /** @var PendingOutput */
    protected $output;

    /** @var object */
    protected $statement;

    /** @var string */
    protected $variable;

    public function __construct(
        Controller $controller,
        string $methodName,
        object $statement,
        PendingOutput $output,
        array $models = []
    ) {
        $this->controller = $controller;
        $this->methodName = $methodName;
        $this->models = $models;
        $this->output = $output;
        $this->statement = $statement;

        $this->context = Str::singular($this->controller->prefix());
        $this->variable = Str::camel($this->context);
    }
}
