<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Blueprint\Models\Column;
use Blueprint\Models\Controller;
use Blueprint\Models\Model;
use Blueprint\Models\Statements\ValidateStatement;
use Fidum\BlueprintPestAddon\Builders\Concerns\DeterminesModels;
use Fidum\BlueprintPestAddon\Builders\PendingOutput;
use Fidum\BlueprintPestAddon\Concerns\PopulatesTestStub;
use Fidum\BlueprintPestAddon\Contracts\TestCaseBuilder;
use Illuminate\Support\Str;
use Shift\Faker\Registry as FakerRegistry;

class ValidateStatementBuilder extends StatementBuilder implements TestCaseBuilder
{
    use DeterminesModels;
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

                /** @var Model|null $localModel */
                $localModel = $this->tree->modelForContext($qualifier);

                if (! is_null($localModel) && $localModel->hasColumn($column)) {
                    $localColumn = $localModel->column($column);
                    if (! $this->generateReferenceFactory($localColumn)) {
                        $faker = sprintf(
                            '$%s = $this->faker->%s;',
                            $data,
                            FakerRegistry::fakerData($localColumn->name())
                                ?? FakerRegistry::fakerDataType($localModel->column($column)->dataType())
                        );

                        $this->output->addSetUp('data', $faker)->addRequestData($variableName, $data);
                    }
                } elseif (! is_null($localModel)) {
                    foreach ($localModel->columns() as $localColumn) {
                        if ($localColumn->name() === 'id') {
                            continue;
                        }

                        if (in_array('nullable', $localColumn->modifiers())) {
                            continue;
                        }

                        if ($this->generateReferenceFactory($localColumn)) {
                            continue;
                        }

                        $faker = sprintf(
                            '$%s = $this->faker->%s;',
                            $localColumn->name(),
                            FakerRegistry::fakerData($localColumn->name())
                                ?? FakerRegistry::fakerDataType($localColumn->dataType())
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

    private function buildFormRequestName(Controller $controller, string $name): string
    {
        if (empty($controller->namespace())) {
            return $controller->name().Str::studly($name).'Request';
        }

        return $controller->namespace().'\\'.$controller->name().Str::studly($name).'Request';
    }

    private function generateReferenceFactory(Column $column): bool
    {
        if (! in_array($column->dataType(), ['id', 'uuid'])
            && ! ($column->attributes() && Str::endsWith($column->name(), '_id'))
        ) {
            return false;
        }

        $reference = Str::beforeLast($column->name(), '_id');
        $variableName = $reference.'->id';

        if ($column->attributes()) {
            $reference = $column->attributes()[0];
        }

        $model = Str::studly($reference);

        $this->output->addImport($this->modelNamespace().'\\'.$model)
            ->addFactory(Str::beforeLast($column->name(), '_id'), $model)
            ->addRequestData($variableName, $column->name());

        return true;
    }

    private function splitField(string $field): array
    {
        if (Str::contains($field, '.')) {
            return explode('.', $field, 2);
        }

        return [null, $field];
    }
}
