<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Statements\SendStatement;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;
use Illuminate\Support\Str;

/**
 * Class SendStatementGenerator
 * @package Fidum\BlueprintPestAddon\Builders\Statements
 * @property
 */
class SendStatementBuilder extends StatementBuilder
{
    /** @var SendStatement */
    protected $statement;

    public function execute(): PendingOutput
    {
        $this->output->addImport('Illuminate\\Support\\Facades\\Mail')
            ->addImport(config('blueprint.namespace').'\\Mail\\'.$this->statement->mail())
            ->addSetUp('mock', 'Mail::fake();');

        $assertion = sprintf('Mail::assertSent(%s::class', $this->statement->mail());

        if ($this->statement->data() || $this->statement->to()) {
            $conditions = [];
            $variables = [];
            $assertion .= ', function ($mail)';

            if ($this->statement->to()) {
                $conditions[] = '$mail->hasTo($'.str_replace('.', '->', $this->statement->to()).')';
            }

            foreach ($this->statement->data() as $data) {
                if (Str::studly(Str::singular($data)) === $this->context) {
                    $variables[] .= '$'.$data;
                    $conditions[] .= sprintf('$mail->%s->is($%s)', $data, $data);
                } else {
                    [$model, $property] = explode('.', $data);
                    $variables[] .= '$'.$model;
                    $conditions[] .= sprintf('$mail->%s == $%s', $property ?? $model,
                        str_replace('.', '->', $data()));
                }
            }

            if ($variables) {
                $assertion .= ' use ('.implode(', ', array_unique($variables)).')';
            }

            $assertion .= ' {'.PHP_EOL;
            $assertion .= str_pad(' ', 8);
            $assertion .= 'return '.implode(' && ', $conditions).';';
            $assertion .= PHP_EOL.str_pad(' ', 4).'}';
        }

        $assertion .= ');';

        return $this->output->addAssertion('mock', $assertion);
    }
}
