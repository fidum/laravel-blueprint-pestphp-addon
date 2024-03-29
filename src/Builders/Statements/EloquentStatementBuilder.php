<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Model;
use Blueprint\Models\Statements\EloquentStatement;
use Fidum\BlueprintPestAddon\Builders\Concerns\DeterminesModels;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;
use Fidum\BlueprintPestAddon\Enums\Coverage;
use Illuminate\Support\Str;

class EloquentStatementBuilder extends StatementBuilder
{
    use DeterminesModels;

    /** @var EloquentStatement */
    protected object $statement;

    public function execute(): PendingOutput
    {
        $model = $this->determineModel($this->controller->prefix(), $this->statement->reference());
        $modelContext = $this->tree->modelForContext($model) ?? new Model($model);
        $this->output->addImport($this->fullyQualifiedModelClassName($model));

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
                    ->addAssertion('sanity', 'expect($'.$plural.')->toHaveCount(1);')
                    ->addAssertion('sanity', sprintf('$%s = $%s->first();', $this->variable, $plural));
            } else {
                $this->output->addAssertion(
                    'generic',
                    '$this->assertDatabaseHas(\''.Str::camel(Str::plural($model)).'\', [ /* ... */ ]);'
                );
            }
        } elseif ($this->statement->operation() === 'find') {
            $this->output->addFactory($this->variable, $model);
        } elseif ($this->statement->operation() === 'delete') {
            $this->output->addCoverage(Coverage::DELETE)
                ->addFactory($this->variable, $model)
                ->addAssertion('generic', $modelContext->usesSoftDeletes()
                    ? sprintf('$this->assertSoftDeleted($%s);', $this->variable)
                    : sprintf('$this->assertModelMissing($%s);', $this->variable));
        } elseif ($this->statement->operation() === 'update') {
            $this->output->addAssertion('sanity', sprintf('$%s->refresh();', $this->variable));
            $requestData = $this->output->requestData();

            if ($requestData) {
                foreach ($requestData as $key => $datum) {
                    if ($modelContext->hasColumn($key) && $modelContext->column($key)->dataType() === 'date') {
                        $this->output->addImport('Carbon\\Carbon');
                        $assertion = sprintf('expect($%s->%s)->toEqual(Carbon::parse(%s));', $this->variable, $key, $datum);
                    } else {
                        $assertion = sprintf('expect($%s->%s)->toBe(%s);', $this->variable, $key, $datum);
                    }

                    $this->output->addAssertion('generic', $assertion);
                }
            }
        }

        return $this->output;
    }
}
