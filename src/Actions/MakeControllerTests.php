<?php

namespace Fidum\BlueprintPestAddon\Actions;

use Blueprint\Blueprint;
use Blueprint\Models\Controller;
use Blueprint\Models\Statements\DispatchStatement;
use Blueprint\Models\Statements\EloquentStatement;
use Blueprint\Models\Statements\FireStatement;
use Blueprint\Models\Statements\RedirectStatement;
use Blueprint\Models\Statements\RenderStatement;
use Blueprint\Models\Statements\SendStatement;
use Blueprint\Models\Statements\SessionStatement;
use Blueprint\Models\Statements\ValidateStatement;
use Fidum\BlueprintPestAddon\Contracts\Action;
use Fidum\BlueprintPestAddon\Traits\HasOutput;
use Fidum\BlueprintPestAddon\Traits\HasStubFile;
use Fidum\BlueprintPestAddon\Traits\PopulatesTestStub;
use Illuminate\Support\Str;

class MakeControllerTests implements Action
{
    use HasOutput;
    use HasStubFile;
    use PopulatesTestStub;

    private $models = [];

    private $imports = [];

    public function execute($files, array $tree): Action
    {
        $this->registerModels($tree);

        $stub = $this->stubFileContent('test.stub');

        foreach ($tree['controllers'] as $controller) {
            $path = $this->getPath($controller);

            if (! $files->exists(dirname($path))) {
                $files->makeDirectory(dirname($path), 0755, true);
            }

            $files->put($path, $this->populateTestStub(
                $stub,
                $this->getNamespace($controller),
                $this->buildTestCases($controller),
                $this->buildImports($controller)
            ));

            $this->created($path);
        }

        return $this;
    }

    private function addImport(Controller $controller, $class)
    {
        $this->imports[$controller->name()][] = $class;
    }

    private function buildImports(Controller $controller)
    {
        $imports = array_unique($this->imports[$controller->name()] ?? []);
        sort($imports);

        return implode(PHP_EOL, array_map(function ($class) {
            return 'use '.$class.';';
        }, $imports));
    }

    private function buildTestCases(Controller $controller)
    {
        $hocCaseStub = $this->stubFileContent('case_hoc.stub');
        $caseStub = $this->stubFileContent('case.stub');
        $testCases = '';

        foreach ($controller->methods() as $name => $statements) {
            $testCase = null;
            $setup = [
                'data' => [],
                'mock' => [],
            ];
            $assertions = [
                'sanity' => [],
                'response' => [],
                'generic' => [],
                'mock' => [],
            ];
            $request_data = [];
            $tested_bits = 0;

            $model = $controller->prefix();
            $context = Str::singular($controller->prefix());
            $variable = Str::camel($context);

            if (in_array($name, ['edit', 'update', 'show', 'destroy'])) {
                $setup['data'][] = sprintf('$%s = factory(%s::class)->create();', $variable, $model);
            }

            foreach ($statements as $statement) {
                if ($statement instanceof SendStatement) {
                    // $this->addImport($controller, 'Illuminate\\Support\\Facades\\Mail');
                    // $this->addImport($controller, config('blueprint.namespace') . '\\Mail\\' . $statement->mail());
                    //
                    // $setup['mock'][] = 'Mail::fake();';
                    //
                    // $assertion = sprintf('Mail::assertSent(%s::class', $statement->mail());
                    //
                    // if ($statement->data() || $statement->to()) {
                    //     $conditions = [];
                    //     $variables = [];
                    //     $assertion .= ', function ($mail)';
                    //
                    //     if ($statement->to()) {
                    //         $conditions[] = '$mail->hasTo($' . str_replace('.', '->', $statement->to()) . ')';
                    //     }
                    //
                    //     foreach ($statement->data() as $data) {
                    //         if (Str::studly(Str::singular($data)) === $context) {
                    //             $variables[] .= '$' . $data;
                    //             $conditions[] .= sprintf('$mail->%s->is($%s)', $data, $data);
                    //         } else {
                    //             [$model, $property] = explode('.', $data);
                    //             $variables[] .= '$' . $model;
                    //             $conditions[] .= sprintf('$mail->%s == $%s', $property ?? $model, str_replace('.', '->', $data()));
                    //         }
                    //     }
                    //
                    //     if ($variables) {
                    //         $assertion .= ' use (' . implode(', ', array_unique($variables)) . ')';
                    //     }
                    //
                    //     $assertion .= ' {' . PHP_EOL;
                    //     $assertion .= str_pad(' ', 12);
                    //     $assertion .= 'return ' . implode(' && ', $conditions) . ';';
                    //     $assertion .= PHP_EOL . str_pad(' ', 8) . '}';
                    // }
                    //
                    // $assertion .= ');';
                    //
                    // $assertions['mock'][] = $assertion;
                } elseif ($statement instanceof ValidateStatement) {
                    $class = $this->buildFormRequestName($controller, $name);
                    $testCase = $this->buildFormRequestTestCase($hocCaseStub, $controller->fullyQualifiedClassName(), $name, config('blueprint.namespace') . '\\Http\\Requests\\' . $class);

                    // if ($statement->data()) {
                    //     $this->addFakerTrait($controller);
                    //
                    //     foreach ($statement->data() as $data) {
                    //         [$qualifier, $column] = $this->splitField($data);
                    //
                    //         if (is_null($qualifier)) {
                    //             $qualifier = $context;
                    //         }
                    //
                    //         /** @var \Blueprint\Models\Model $model */
                    //         $local_model = $this->modelForContext($qualifier);
                    //         if (!is_null($local_model) && $local_model->hasColumn($column)) {
                    //             $faker = FactoryGenerator::fakerData($local_model->column($column)->name()) ?? FactoryGenerator::fakerDataType($local_model->column($column)->dataType());
                    //         } else {
                    //             $faker = 'word';
                    //         }
                    //
                    //         $setup['data'][] = sprintf('$%s = $this->faker->%s;', $data, $faker);
                    //         $request_data[$data] = '$' . $data;
                    //     }
                    // }
                } elseif ($statement instanceof DispatchStatement) {
                    // $this->addImport($controller, 'Illuminate\\Support\\Facades\\Queue');
                    // $this->addImport($controller, config('blueprint.namespace') . '\\Jobs\\' . $statement->job());
                    //
                    // $setup['mock'][] = 'Queue::fake();';
                    //
                    // $assertion = sprintf('Queue::assertPushed(%s::class', $statement->job());
                    //
                    // if ($statement->data()) {
                    //     $conditions = [];
                    //     $variables = [];
                    //     $assertion .= ', function ($job)';
                    //
                    //     foreach ($statement->data() as $data) {
                    //         if (Str::studly(Str::singular($data)) === $context) {
                    //             $variables[] .= '$' . $data;
                    //             $conditions[] .= sprintf('$job->%s->is($%s)', $data, $data);
                    //         } else {
                    //             [$model, $property] = explode('.', $data);
                    //             $variables[] .= '$' . $model;
                    //             $conditions[] .= sprintf('$job->%s == $%s', $property ?? $model, str_replace('.', '->', $data()));
                    //         }
                    //     }
                    //
                    //     if ($variables) {
                    //         $assertion .= ' use (' . implode(', ', array_unique($variables)) . ')';
                    //     }
                    //
                    //     $assertion .= ' {' . PHP_EOL;
                    //     $assertion .= str_pad(' ', 12);
                    //     $assertion .= 'return ' . implode(' && ', $conditions) . ';';
                    //     $assertion .= PHP_EOL . str_pad(' ', 8) . '}';
                    // }
                    //
                    // $assertion .= ');';
                    //
                    // $assertions['mock'][] = $assertion;
                } elseif ($statement instanceof FireStatement) {
                    // $this->addImport($controller, 'Illuminate\\Support\\Facades\\Event');
                    //
                    // $setup['mock'][] = 'Event::fake();';
                    //
                    // $assertion = 'Event::assertDispatched(';
                    //
                    // if ($statement->isNamedEvent()) {
                    //     $assertion .= $statement->event();
                    // } else {
                    //     $this->addImport($controller, config('blueprint.namespace') . '\\Events\\' . $statement->event());
                    //     $assertion .= $statement->event() . '::class';
                    // }
                    //
                    // if ($statement->data()) {
                    //     $conditions = [];
                    //     $variables = [];
                    //     $assertion .= ', function ($event)';
                    //
                    //     foreach ($statement->data() as $data) {
                    //         if (Str::studly(Str::singular($data)) === $context) {
                    //             $variables[] .= '$' . $data;
                    //             $conditions[] .= sprintf('$event->%s->is($%s)', $data, $data);
                    //         } else {
                    //             [$model, $property] = explode('.', $data);
                    //             $variables[] .= '$' . $model;
                    //             $conditions[] .= sprintf('$event->%s == $%s', $property ?? $model, str_replace('.', '->', $data()));
                    //         }
                    //     }
                    //
                    //     if ($variables) {
                    //         $assertion .= ' use (' . implode(', ', array_unique($variables)) . ')';
                    //     }
                    //
                    //     $assertion .= ' {' . PHP_EOL;
                    //     $assertion .= str_pad(' ', 12);
                    //     $assertion .= 'return ' . implode(' && ', $conditions) . ';';
                    //     $assertion .= PHP_EOL . str_pad(' ', 8) . '}';
                    // }
                    //
                    // $assertion .= ');';
                    //
                    // $assertions['mock'][] = $assertion;
                } elseif ($statement instanceof RenderStatement) {
                    // $tested_bits |= self::TESTS_VIEW;
                    //
                    // $view_assertions = [];
                    // $view_assertions[] = '$response->assertOk();';
                    // $view_assertions[] = sprintf('$response->assertViewIs(\'%s\');', $statement->view());
                    //
                    // foreach ($statement->data() as $data) {
                    //     // TODO: if data references locally scoped var, strengthen assertion...
                    //     $view_assertions[] = sprintf('$response->assertViewHas(\'%s\');', $data);
                    // }
                    //
                    //
                    // array_unshift($assertions['response'], ...$view_assertions);
                } elseif ($statement instanceof RedirectStatement) {
                //     $tested_bits |= self::TESTS_REDIRECT;
                //
                //     $assertion = sprintf('$response->assertRedirect(route(\'%s\'', $statement->route());
                //
                //     if ($statement->data()) {
                //         $parameters = array_map(function ($parameter) {
                //             return '$' . $parameter;
                //         }, $statement->data());
                //
                //         $assertion .= ', [' . implode(', ', $parameters) . ']';
                //     } elseif (Str::contains($statement->route(), '.')) {
                //         [$model, $action] = explode('.', $statement->route());
                //         if (in_array($action, ['edit', 'update', 'show', 'destroy'])) {
                //             $assertion .= sprintf(", ['%s' => $%s]", $model, $model);
                //         }
                //     }
                //
                //     $assertion .= '));';
                //
                //     array_unshift($assertions['response'], $assertion);
                // } elseif ($statement instanceof RespondStatement) {
                //     $tested_bits |= self::TESTS_RESPONDS;
                //
                //     if ($statement->content()) {
                //         array_unshift($assertions['response'], '$response->assertJson($' . $statement->content() . ');');
                //     }
                //
                //     if ($statement->status() === 200) {
                //         array_unshift($assertions['response'], '$response->assertOk();');
                //     } elseif ($statement->status() === 204) {
                //         array_unshift($assertions['response'], '$response->assertNoContent();');
                //     } else {
                //         array_unshift($assertions['response'], '$response->assertNoContent(' . $statement->status() . ');');
                //     }
                } elseif ($statement instanceof SessionStatement) {
                    // $assertions['response'][] = sprintf('$response->assertSessionHas(\'%s\', %s);', $statement->reference(), '$' . str_replace('.', '->', $statement->reference()));
                } elseif ($statement instanceof EloquentStatement) {
                //     $this->addRefreshDatabaseTrait($controller);
                //
                //     $model = $this->determineModel($controller->prefix(), $statement->reference());
                //     $this->addImport($controller, config('blueprint.namespace') . '\\' . $model);
                //
                //     if ($statement->operation() === 'save') {
                //         $tested_bits |= self::TESTS_SAVE;
                //
                //         if ($request_data) {
                //             $indent = str_pad(' ', 12);
                //             $plural = Str::plural($variable);
                //             $assertion = sprintf('$%s = %s::query()', $plural, $model);
                //             foreach ($request_data as $key => $datum) {
                //                 $assertion .= PHP_EOL . sprintf('%s->where(\'%s\', %s)', $indent, $key, $datum);
                //             }
                //             $assertion .= PHP_EOL . $indent . '->get();';
                //
                //             $assertions['sanity'][] = $assertion;
                //             $assertions['sanity'][] = '$this->assertCount(1, $' . $plural . ');';
                //             $assertions['sanity'][] = sprintf('$%s = $%s->first();', $variable, $plural);
                //         } else {
                //             $assertions['generic'][] = '$this->assertDatabaseHas(' . Str::camel(Str::plural($model)) . ', [ /* ... */ ]);';
                //         }
                //     } elseif ($statement->operation() === 'find') {
                //         $setup['data'][] = sprintf('$%s = factory(%s::class)->create();', $variable, $model);
                //     } elseif ($statement->operation() === 'delete') {
                //         $tested_bits |= self::TESTS_DELETE;
                //         $setup['data'][] = sprintf('$%s = factory(%s::class)->create();', $variable, $model);
                //         $assertions['generic'][] = sprintf('$this->assertDeleted($%s);', $variable);
                //     }
                // } elseif ($statement instanceof QueryStatement) {
                //     $this->addRefreshDatabaseTrait($controller);
                //
                //     $setup['data'][] = sprintf('$%s = factory(%s::class, 3)->create();', Str::plural($variable), $model);
                //
                //     $this->addImport($controller, config('blueprint.namespace') . '\\' . $this->determineModel($controller->prefix(), $statement->model()));
                // }
            }

            // $call = sprintf('$response = $this->%s(route(\'%s.%s\'', $this->httpMethodForAction($name), Str::kebab($context), $name);
            //
            // if (in_array($name, ['edit', 'update', 'show', 'destroy'])) {
            //     $call .= ', $' . Str::camel($context);
            // }
            // $call .= ')';
            //
            // if ($request_data) {
            //     $call .= ', [';
            //     $call .= PHP_EOL;
            //     foreach ($request_data as $key => $datum) {
            //         $call .= str_pad(' ', 12);
            //         $call .= sprintf('\'%s\' => %s,', $key, $datum);
            //         $call .= PHP_EOL;
            //     }
            //
            //     $call .= str_pad(' ', 8) . ']';
            }
            // $call .= ');';
            //
            // $body = implode(PHP_EOL . PHP_EOL, array_map([$this, 'buildLines'], $this->uniqueSetupLines($setup)));
            // $body .= PHP_EOL . PHP_EOL;
            // $body .= str_pad(' ', 8) . $call;
            // $body .= PHP_EOL . PHP_EOL;
            // $body .= implode(PHP_EOL . PHP_EOL, array_map([$this, 'buildLines'], array_filter($assertions)));
            //
            // $testCase = str_replace('dummy_test_case', $this->buildTestCaseName($name, $tested_bits), $testCase);
            // $testCase = str_replace('// ...', trim($body), $testCase);

            if ($testCase) {
                $testCases .= PHP_EOL . $testCase . PHP_EOL;
            }
        }

        return trim($testCases);
    }

    private function buildFormRequestName(Controller $controller, string $name)
    {
        if (empty($controller->namespace())) {
            return $controller->name() . Str::studly($name) . 'Request';
        }

        return $controller->namespace() . '\\' . $controller->name() . Str::studly($name) . 'Request';
    }

    private function buildFormRequestTestCase(string $stub, string $controller, string $action, string $formRequest)
    {
        $assertion =  <<<END
->assertActionUsesFormRequest(
        \\${controller}::class,
        '${action}',
        \\${formRequest}::class
    )
END;

        return $this->populateTestCaseStub($stub, "uses form request validation on {$action}", $assertion);
    }

    private function getNamespace(Controller $controller)
    {
        return 'Tests\\Feature\\'.Blueprint::relativeNamespace($controller->fullyQualifiedNamespace());
    }

    private function getPath(Controller $controller)
    {
        // TODO: Use Blueprints TestGenerator?
        $path = str_replace('\\', '/', Blueprint::relativeNamespace($controller->fullyQualifiedClassName()));

        return 'tests/Feature/'.$path.'Test.php';
    }

    private function registerModels(array $tree)
    {
        $this->models = array_merge($tree['cache'] ?? [], $tree['models'] ?? []);
    }
}
