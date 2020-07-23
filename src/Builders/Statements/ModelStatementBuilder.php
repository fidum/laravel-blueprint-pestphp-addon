<?php

namespace Fidum\BlueprintPestAddon\Builders\Statements;

use Illuminate\Support\Str;

abstract class ModelStatementBuilder extends StatementBuilder
{
    protected function determineModel(string $prefix, ?string $reference): string
    {
        if (empty($reference) || $reference === 'id') {
            return Str::studly(Str::singular($prefix));
        }

        if (Str::contains($reference, '.')) {
            return Str::studly(Str::before($reference, '.'));
        }

        return Str::studly($reference);
    }

    protected function modelNamespace(): string
    {
        return config('blueprint.models_namespace')
            ? config('blueprint.namespace').'\\'.config('blueprint.models_namespace')
            : config('blueprint.namespace');
    }
}
