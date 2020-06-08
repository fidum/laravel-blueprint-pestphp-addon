<?php

namespace Fidum\BlueprintPestAddon\Builders;

class PendingOutput
{
    protected $assertions = [
        'sanity' => [],
        'response' => [],
        'generic' => [],
        'mock' => [],
    ];

    protected $imports = [];

    protected $requestData = [];

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

    public function addImport($class): self
    {
        $this->imports[] = $class;

        return $this;
    }

    public function addRequestData(string $field): self
    {
        $this->requestData[$field] = '$'.$field;

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
}
