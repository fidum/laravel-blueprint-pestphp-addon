<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Illuminate\Support\Str;

abstract class ModelStatementBuilder extends StatementBuilder
{
    protected function determineModel(string $prefix, ?string $reference)
    {
        if (empty($reference) || $reference === 'id') {
            return Str::studly(Str::singular($prefix));
        }

        if (Str::contains($reference, '.')) {
            return Str::studly(Str::before($reference, '.'));
        }

        return Str::studly($reference);
    }

    protected function modelForContext(string $context)
    {
        if (isset($this->models[Str::studly($context)])) {
            return $this->models[Str::studly($context)];
        }

        $matches = array_filter(array_keys($this->models), function ($key) use ($context) {
            return Str::endsWith($key, '/'.Str::studly($context));
        });

        if (count($matches) === 1) {
            return $this->models[$matches[0]];
        }

        return null;
    }

    protected function modelNamespace(): string
    {
        return config('blueprint.models_namespace')
            ? config('blueprint.namespace').'\\'.config('blueprint.models_namespace')
            : config('blueprint.namespace');
    }
}
