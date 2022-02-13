<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Statements\FireStatement;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;
use Illuminate\Support\Str;

class FireStatementBuilder extends StatementBuilder
{
    /** @var FireStatement */
    protected object $statement;

    public function execute(): PendingOutput
    {
        $this->output->addImport('Illuminate\\Support\\Facades\\Event')
            ->addSetUp('mock', 'Event::fake();');

        $assertion = 'Event::assertDispatched(';

        if ($this->statement->isNamedEvent()) {
            $assertion .= $this->statement->event();
        } else {
            $this->output->addImport(config('blueprint.namespace').'\\Events\\'.$this->statement->event());
            $assertion .= $this->statement->event().'::class';
        }

        if ($this->statement->data()) {
            $conditions = [];
            $variables = [];
            $assertion .= ', function ($event)';

            foreach ($this->statement->data() as $data) {
                if (Str::studly(Str::singular($data)) === $this->context) {
                    $variables[] .= '$'.$data;
                    $conditions[] .= sprintf('$event->%s->is($%s)', $data, $data);
                } else {
                    [$model, $property] = explode('.', $data);
                    $variables[] .= '$'.$model;
                    $conditions[] .= sprintf('$event->%s == $%s', $property ?: $model,
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
