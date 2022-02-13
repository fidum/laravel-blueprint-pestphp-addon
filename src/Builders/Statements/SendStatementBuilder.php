<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Statements\SendStatement;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;
use Illuminate\Support\Str;

class SendStatementBuilder extends StatementBuilder
{
    /** @var SendStatement */
    protected object $statement;

    public function execute(): PendingOutput
    {
        $this->statement->isNotification()
            ? $this->buildNotificationAssertion()
            : $this->buildMailAssertion();

        return $this->output;
    }

    private function buildNotificationAssertion(): void
    {
        $this->output->addImport('Illuminate\\Support\\Facades\\Notification')
            ->addImport(config('blueprint.namespace').'\\Notification\\'.$this->statement->mail())
            ->addSetUp('mock', 'Notification::fake();');

        $assertion = sprintf(
            'Notification::assertSentTo($%s, %s::class',
            str_replace('.', '->', $this->statement->to()),
            $this->statement->mail()
        );

        if ($this->statement->data()) {
            $conditions = [];
            $variables = [];
            $assertion .= ', function ($notification)';

            foreach ($this->statement->data() as $data) {
                if (Str::studly(Str::singular($data)) === $this->context) {
                    $variables[] .= '$'.$data;
                    $conditions[] .= sprintf('$notification->%s->is($%s)', $data, $data);
                } else {
                    [$model, $property] = explode('.', $data);
                    $variables[] .= '$'.$model;
                    $conditions[] .= sprintf('$notification->%s == $%s', $property ?: $model, str_replace('.', '->', $data()));
                }
            }

            $assertion .= ' use ('.implode(', ', array_unique($variables)).')';
            $assertion .= ' {'.PHP_EOL;
            $assertion .= str_pad(' ', 8);
            $assertion .= 'return '.implode(' && ', $conditions).';';
            $assertion .= PHP_EOL.str_pad(' ', 4).'}';
        }

        $assertion .= ');';

        $this->output->addAssertion('mock', $assertion);
    }

    private function buildMailAssertion(): void
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
                    $conditions[] .= sprintf('$mail->%s == $%s', $property ?: $model,
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

        $this->output->addAssertion('mock', $assertion);
    }
}
