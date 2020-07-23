<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Statements\EloquentStatement;
use Fidum\BlueprintPestAddon\Builders\Concerns\ModelStatementHelper;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;
use Fidum\BlueprintPestAddon\Enums\Coverage;
use Illuminate\Support\Str;

class EloquentStatementBuilder extends StatementBuilder
{
    use ModelStatementHelper;

    /** @var EloquentStatement */
    protected $statement;

    public function execute(): PendingOutput
    {
        $model = $this->determineModel($this->controller->prefix(), $this->statement->reference());
        $this->output->addImport($this->modelNamespace().'\\'.$model);

        if ($this->statement->operation() === 'save') {
            $this->output->addCoverage(Coverage::SAVE);

            $requestData = $this->output->requestData();

            if ($requestData) {
                $indent = str_pad(' ', 8);
                $plural = Str::plural($this->variable);
                $assertion = sprintf('$%s = %s::query()', $plural, $model);
                foreach ($requestData as $key => $datum) {
                    $assertion .= PHP_EOL.sprintf('%s->where(\'%s\', %s)', $indent, $key, $datum);
                }
                $assertion .= PHP_EOL.$indent.'->get();';

                $this->output->addAssertion('sanity', $assertion)
                    ->addAssertion('sanity', 'assertCount(1, $'.$plural.');')
                    ->addAssertion('sanity', sprintf('$%s = $%s->first();', $this->variable, $plural));
            } else {
                $this->output->addAssertion(
                    'generic',
                    '$this->assertDatabaseHas(\''.Str::camel(Str::plural($model)).'\', [ /* ... */ ]);'
                );
            }
        } elseif ($this->statement->operation() === 'find') {
            $this->output->addSetUp('data', sprintf('$%s = factory(%s::class)->create();', $this->variable, $model));
        } elseif ($this->statement->operation() === 'delete') {
            $this->output->addCoverage(Coverage::DELETE)
                ->addSetUp('data', sprintf('$%s = factory(%s::class)->create();', $this->variable, $model))
                ->addAssertion('generic', sprintf('$this->assertDeleted($%s);', $this->variable));
        } elseif ($this->statement->operation() === 'update') {
            $this->output->addAssertion('sanity', sprintf('$%s->refresh();', $this->variable));
            $requestData = $this->output->requestData();

            if ($requestData) {
                foreach ($requestData as $key => $datum) {
                    $assertion = sprintf('assertSame(%s, $%s->%s);', $datum, $this->variable, $key);
                    $this->output->addAssertion('generic', $assertion);
                }
            }
        }

        return $this->output;
    }
}
