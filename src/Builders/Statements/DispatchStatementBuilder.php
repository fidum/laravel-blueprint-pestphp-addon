<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Statements\DispatchStatement;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;
use Illuminate\Support\Str;

class DispatchStatementBuilder extends StatementBuilder
{
    /** @var DispatchStatement */
    protected $statement;

    public function execute(): PendingOutput
    {
        $this->output->addImport('Illuminate\\Support\\Facades\\Queue')
            ->addImport(config('blueprint.namespace').'\\Jobs\\'.$this->statement->job())
            ->addSetUp('mock', 'Queue::fake();');

        $assertion = sprintf('Queue::assertPushed(%s::class', $this->statement->job());

        if ($this->statement->data()) {
            $conditions = [];
            $variables = [];
            $assertion .= ', function ($job)';

            foreach ($this->statement->data() as $data) {
                if (Str::studly(Str::singular($data)) === $this->context) {
                    $variables[] .= '$'.$data;
                    $conditions[] .= sprintf('$job->%s->is($%s)', $data, $data);
                } else {
                    [$model, $property] = explode('.', $data);
                    $variables[] .= '$'.$model;
                    $conditions[] .= sprintf('$job->%s == $%s', $property ?: $model,
                        str_replace('.', '->', $data()));
                }
            }

            $assertion .= ' use ('.implode(', ', array_unique($variables)).')';
            $assertion .= ' {'.PHP_EOL;
            $assertion .= str_pad(' ', 8);
            $assertion .= 'return '.implode(' && ', $conditions).';';
            $assertion .= PHP_EOL.str_pad(' ', 4).'}';
        }

        $assertion .= ');';

        return $this->output->addAssertion('mock', $assertion);
    }
}
