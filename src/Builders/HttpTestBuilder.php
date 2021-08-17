<?php

namespace Fidum\BlueprintPestAddon\Builders;

use Blueprint\Models\Controller;
use Blueprint\Models\Statements\DispatchStatement;
use Blueprint\Models\Statements\EloquentStatement;
use Blueprint\Models\Statements\FireStatement;
use Blueprint\Models\Statements\QueryStatement;
use Blueprint\Models\Statements\RedirectStatement;
use Blueprint\Models\Statements\RenderStatement;
use Blueprint\Models\Statements\ResourceStatement;
use Blueprint\Models\Statements\RespondStatement;
use Blueprint\Models\Statements\SendStatement;
use Blueprint\Models\Statements\SessionStatement;
use Blueprint\Models\Statements\ValidateStatement;
use Blueprint\Tree;
use Fidum\BlueprintPestAddon\Builders\Statements\DispatchStatementBuilder;
use Fidum\BlueprintPestAddon\Builders\Statements\EloquentStatementBuilder;
use Fidum\BlueprintPestAddon\Builders\Statements\FireStatementBuilder;
use Fidum\BlueprintPestAddon\Builders\Statements\InitialStatementBuilder;
use Fidum\BlueprintPestAddon\Builders\Statements\QueryStatementBuilder;
use Fidum\BlueprintPestAddon\Builders\Statements\RedirectStatementBuilder;
use Fidum\BlueprintPestAddon\Builders\Statements\RenderStatementBuilder;
use Fidum\BlueprintPestAddon\Builders\Statements\ResourceStatementBuilder;
use Fidum\BlueprintPestAddon\Builders\Statements\RespondStatementBuilder;
use Fidum\BlueprintPestAddon\Builders\Statements\SendStatementBuilder;
use Fidum\BlueprintPestAddon\Builders\Statements\SessionStatementBuilder;
use Fidum\BlueprintPestAddon\Builders\Statements\ValidateStatementBuilder;
use Fidum\BlueprintPestAddon\Concerns\PopulatesTestStub;
use Fidum\BlueprintPestAddon\Concerns\ReadsStubFiles;
use Fidum\BlueprintPestAddon\Contracts\StatementBuilder;
use Fidum\BlueprintPestAddon\Contracts\TestCaseBuilder;
use Fidum\BlueprintPestAddon\Enums\Coverage;
use Illuminate\Support\Str;

class HttpTestBuilder
{
    use ReadsStubFiles;
    use PopulatesTestStub;

    /** @var array */
    private $imports = [];

    /** @var array */
    private $builders = [
        SendStatement::class => SendStatementBuilder::class,
        ValidateStatement::class => ValidateStatementBuilder::class,
        DispatchStatement::class => DispatchStatementBuilder::class,
        FireStatement::class => FireStatementBuilder::class,
        RenderStatement::class => RenderStatementBuilder::class,
        RedirectStatement::class => RedirectStatementBuilder::class,
        ResourceStatement::class => ResourceStatementBuilder::class,
        RespondStatement::class => RespondStatementBuilder::class,
        SessionStatement::class => SessionStatementBuilder::class,
        EloquentStatement::class => EloquentStatementBuilder::class,
        QueryStatement::class => QueryStatementBuilder::class,
    ];

    /** @var Tree */
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function imports(Controller $controller): string
    {
        $imports = array_unique($this->imports[$controller->name()] ?? []);
        sort($imports);

        $suffix = count($imports) ? PHP_EOL.PHP_EOL : '';

        return implode(PHP_EOL, array_map(function ($class) {
            return 'use '.$class.';';
        }, $imports)).$suffix;
    }

    public function testCases(Controller $controller): string
    {
        $hocCaseStub = $this->stubFileContent('case_hoc.stub');
        $caseStub = $this->stubFileContent('case.stub');
        $testCases = '';

        foreach ($controller->methods() as $name => $statements) {
            $output = new PendingOutput();

            (new InitialStatementBuilder($controller, $name, $output, $this->tree))->execute();

            foreach ($statements as $statement) {
                $class = $this->builders[get_class($statement)] ?? null;

                if ($class) {
                    /** @var StatementBuilder $builder */
                    $builder = new $class($controller, $name, $statement, $output, $this->tree);
                    $builder->execute();

                    if ($builder instanceof TestCaseBuilder) {
                        $testCases .= PHP_EOL.$builder->testCase($hocCaseStub).PHP_EOL;
                    }
                }
            }

            $cName = $controller->name();
            $context = PendingOutput::context($controller);

            $this->imports[$cName] = array_merge($this->imports[$cName] ?? [], $output->imports());
            $assertions = $output->assertions();
            $requestData = $output->requestData();
            $setup = $output->setUp();
            $coverage = $output->coverage();

            $call = sprintf('$response = $this->%s(route(\'%s.%s\'', $this->httpMethodForAction($name),
                Str::kebab($context), $name);

            if (in_array($name, ['edit', 'update', 'show', 'destroy'])) {
                $call .= ', $'.Str::camel($context);
            }
            $call .= ')';

            if ($requestData) {
                $call .= ', [';
                $call .= PHP_EOL;
                foreach ($requestData as $key => $datum) {
                    $call .= str_pad(' ', 8);
                    $call .= sprintf('\'%s\' => %s,', $key, $datum);
                    $call .= PHP_EOL;
                }

                $call .= str_pad(' ', 4).']';
            }
            $call .= ');';

            $body = implode(PHP_EOL.PHP_EOL, array_map([$this, 'buildLines'], $this->uniqueSetupLines($setup)));
            $body .= PHP_EOL.PHP_EOL;
            $body .= str_pad(' ', 4).$call;
            $body .= PHP_EOL.PHP_EOL;
            $body .= implode(PHP_EOL.PHP_EOL, array_map([$this, 'buildLines'], array_filter($assertions)));

            $testCase = $this->populateTestCaseStub(
                $caseStub,
                $this->buildTestCaseName($name, $coverage),
                trim($body)
            );

            if ($testCase) {
                $testCases .= PHP_EOL.$testCase.PHP_EOL;
            }
        }

        return trim($testCases);
    }

    private function buildLines(array $lines): string
    {
        return str_pad(' ', 4).implode(PHP_EOL.str_pad(' ', 4), $lines);
    }

    private function buildTestCaseName(string $name, int $coverage): string
    {
        $verifications = [];

        if ($coverage & Coverage::SAVE) {
            $verifications[] = 'saves';
        }

        if ($coverage & Coverage::DELETE) {
            $verifications[] = 'deletes';
        }

        if ($coverage & Coverage::VIEW) {
            $verifications[] = 'displays view';
        }

        if ($coverage & Coverage::REDIRECT) {
            $verifications[] = 'redirects';
        }

        if ($coverage & Coverage::RESPONDS) {
            $verifications[] = 'responds with';
        }

        if (empty($verifications)) {
            return $name.' behaves as expected';
        }

        $final_verification = array_pop($verifications).' on '.$name;

        if (empty($verifications)) {
            return $final_verification;
        }

        return implode(' ', $verifications).' and '.$final_verification;
    }

    private function httpMethodForAction(string $action): string
    {
        switch ($action) {
            case 'store':
                return 'post';
            case 'update':
                return 'put';
            case 'destroy':
                return 'delete';
            default:
                return 'get';
        }
    }

    private function uniqueSetupLines(array $setup): array
    {
        return collect($setup)->filter()
            ->map(function (array $lines) {
                return array_unique($lines);
            })
            ->toArray();
    }
}
