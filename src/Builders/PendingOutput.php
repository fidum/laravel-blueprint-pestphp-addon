<?php

namespace Fidum\BlueprintPestAddon\Builders;

use Blueprint\Models\Controller;
use Fidum\BlueprintPestAddon\Builders\Concerns\BuildsFactoryStatements;
use Fidum\BlueprintPestAddon\Builders\Concerns\DeterminesLaravelVersion;
use Illuminate\Support\Str;

class PendingOutput
{
    use BuildsFactoryStatements;
    use DeterminesLaravelVersion;

    /** @var array[] */
    protected $assertions = [
        'sanity' => [],
        'response' => [],
        'generic' => [],
        'mock' => [],
    ];

    /** @var array */
    protected $imports = [];

    /** @var array */
    protected $requestData = [];

    /** @var array[] */
    protected $setUp = [
        'data' => [],
        'mock' => [],
    ];

    /** @var int */
    private $coverage;

    public function __construct(int $coverage = 0)
    {
        $this->coverage = $coverage;
    }

    public function addAssertion(string $type, string $content, bool $unshift = false): self
    {
        if ($unshift) {
            array_unshift($this->assertions[$type], $content);

            return $this;
        }

        $this->assertions[$type][] = $content;

        return $this;
    }

    public function addAssertions(string $type, array $assertions): self
    {
        array_unshift($this->assertions[$type], ...$assertions);

        return $this;
    }

    public function addFactory(string $variable, string $model, int $count = 1): self
    {
        $statement = static::isLaravel8OrHigher()
            ? $this->classFactory($variable, $model, $count)
            : $this->legacyFactory($variable, $model, $count);

        return $this->addSetUp('data', $statement);
    }

    public function addImport(string $class): self
    {
        $this->imports[] = $class;

        return $this;
    }

    public function addRequestData(string $field, string $key = null): self
    {
        $this->requestData[$key ?? $field] = '$'.$field;

        return $this;
    }

    public function addSetUp(string $type, string $content): self
    {
        $this->setUp[$type][] = $content;

        return $this;
    }

    public function addCoverage(int $area): self
    {
        $this->coverage |= $area;

        return $this;
    }

    public function assertions(): array
    {
        return $this->assertions;
    }

    public function imports(): array
    {
        return $this->imports;
    }

    public function requestData(): array
    {
        return $this->requestData;
    }

    public function setUp(): array
    {
        return $this->setUp;
    }

    public function coverage(): int
    {
        return $this->coverage;
    }

    public static function context(Controller $controller): string
    {
        return Str::singular($controller->prefix());
    }
}
