<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Generators\FactoryGenerator;
use Blueprint\Models\Column;
use Blueprint\Models\Controller;
use Blueprint\Models\Model;
use Blueprint\Models\Statements\ValidateStatement;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;
use Fidum\BlueprintPestAddon\Contracts\TestCaseBuilder;
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
            foreach ($this->statement->data() as $data) {
                [$qualifier, $column] = $this->splitField($data);

                if (is_null($qualifier)) {
                    $qualifier = $this->context;
                }

                $variableName = $data;

                /** @var Model $localModel */
                $localModel = $this->modelForContext($qualifier);

                if (! is_null($localModel) && $localModel->hasColumn($column)) {
                    $localColumn = $localModel->column($column);
                    if (! $this->generateRelationFactory($localColumn)) {
                        $faker = sprintf(
                            '$%s = $this->faker->%s;',
                            $data,
                            FactoryGenerator::fakerData($localColumn->name())
                                ?? FactoryGenerator::fakerDataType($localModel->column($column)->dataType())
                        );

                        $this->output->addSetUp('data', $faker)->addRequestData($variableName, $data);
                    }
                } else {
                    foreach ($localModel->columns() as $localColumn) {
                        if ($localColumn->name() === 'id') {
                            continue;
                        }

                        if (in_array('nullable', $localColumn->modifiers())) {
                            continue;
                        }

                        if ($this->generateRelationFactory($localColumn)) {
                            continue;
                        }

                        $faker = sprintf(
                            '$%s = $this->faker->%s;',
                            $localColumn->name(),
                            FactoryGenerator::fakerData($localColumn->name())
                                ?? FactoryGenerator::fakerDataType($localColumn->dataType())
                        );

                        $this->output->addSetUp('data', $faker)->addRequestData($localColumn->name());
                    }
                }
            }
        }

        return $this->output;
    }

    public function testCase(string $stub): string
    {
        $ns = config('blueprint.namespace').'\\Http\\Requests\\';
        $class = $this->buildFormRequestName($this->controller, $this->methodName);
        $requestClassName = Str::afterLast($class, '\\');

        $this->output->addImport($this->controller->fullyQualifiedClassName())
            ->addImport($ns.$class);

        $assertion = <<<END
->assertActionUsesFormRequest(
        {$this->controller->className()}::class,
        '{$this->methodName}',
        {$requestClassName}::class
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

    private function generateRelationFactory(Column $column): bool
    {
        if (
            ($column->dataType() === 'id' || $column->dataType() === 'uuid')
            && ($column->attributes() && Str::endsWith($column->name(), '_id'))
        ) {
            $variableName = Str::beforeLast($column->name(), '_id');
            $reference = $variableName;

            if ($column->attributes()) {
                $reference = $column->attributes()[0];
                $variableName .= '->id';
            }

            $faker = sprintf(
                '$%s = factory(%s::class)->create();',
                Str::beforeLast($column->name(), '_id'),
                Str::studly($reference)
            );

            $this->output->addImport($this->modelNamespace().'\\'.Str::studly($reference))
                ->addSetUp('data', $faker)
                ->addRequestData($variableName, $column->name());

            return true;
        }

        return false;
    }

    private function splitField($field)
    {
        if (Str::contains($field, '.')) {
            return explode('.', $field, 2);
        }

        return [null, $field];
    }
}
