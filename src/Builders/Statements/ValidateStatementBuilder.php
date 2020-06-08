<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Generators\FactoryGenerator;
use Blueprint\Models\Controller;
use Blueprint\Models\Statements\ValidateStatement;
use Fidum\BlueprintPestAddon\Contracts\TestCaseBuilder;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;
use Fidum\BlueprintPestAddon\Traits\PopulatesTestStub;
use Illuminate\Support\Str;

class ValidateStatementBuilder extends ModelStatementBuilder implements TestCaseBuilder
{
    use PopulatesTestStub;

    /** @var ValidateStatement */
    protected $statement;

    public function execute(): PendingOutput
    {
        if ($this->statement->data()) {
            foreach ($this->statement->data() as $field) {
                [$qualifier, $column] = $this->splitField($field);

                if (is_null($qualifier)) {
                    $qualifier = $this->context;
                }

                /** @var \Blueprint\Models\Model $model */
                $model = $this->modelForContext($qualifier);
                if (! is_null($model) && $model->hasColumn($column)) {
                    $faker = FactoryGenerator::fakerData($model->column($column)->name())
                        ?? FactoryGenerator::fakerDataType($model->column($column)->dataType());
                } else {
                    $faker = 'word';
                }

                $this->output->addSetUp('data', sprintf('$%s = $this->faker->%s;', $field, $faker))
                    ->addRequestData($field);
            }
        }

        return $this->output;
    }

    public function testCase(string $stub): string
    {
        $ns = config('blueprint.namespace').'\\Http\\Requests\\';
        $class = $this->buildFormRequestName($this->controller, $this->methodName);

        $assertion = <<<END
->assertActionUsesFormRequest(
        \\{$this->controller->fullyQualifiedClassName()}::class,
        '{$this->methodName}',
        \\{$ns}{$class}::class
    )
END;

        return $this->populateTestCaseStub($stub, "uses form request validation on {$this->methodName}", $assertion);
    }

    private function buildFormRequestName(Controller $controller, string $name)
    {
        if (empty($controller->namespace())) {
            return $controller->name().Str::studly($name).'Request';
        }

        return $controller->namespace().'\\'.$controller->name().Str::studly($name).'Request';
    }

    private function splitField($field)
    {
        if (Str::contains($field, '.')) {
            return explode('.', $field, 2);
        }

        return [null, $field];
    }
}
