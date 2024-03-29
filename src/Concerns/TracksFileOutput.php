<?php

namespace Fidum\BlueprintPestAddon\Concerns;

trait TracksFileOutput
{
    private array $output = [];

    private function created(string $filePath): void
    {
        $this->addOutput($filePath, false);
    }

    private function updated(string $filePath): void
    {
        $this->addOutput($filePath, true);
    }

    private function addOutput(string $filePath, bool $updated = false): void
    {
        $key = $updated ? 'updated' : 'created';
        $this->output[$key][] = $filePath;
    }

    public function output(): array
    {
        return $this->output;
    }
}
