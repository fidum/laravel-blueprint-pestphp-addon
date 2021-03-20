<?php

namespace Fidum\BlueprintPestAddon\Builders\Concerns;

use Blueprint\Models\Model;
use Illuminate\Support\Str;

trait DeterminesModels
{
    private function determineModel(string $prefix, ?string $reference): string
    {
        if (empty($reference) || $reference === 'id') {
            return Str::studly(Str::singular($prefix));
        }

        if (Str::contains($reference, '.')) {
            return Str::studly(Str::before($reference, '.'));
        }

        return Str::studly($reference);
    }

    private function modelNamespace(): string
    {
        return config('blueprint.models_namespace')
            ? config('blueprint.namespace').'\\'.config('blueprint.models_namespace')
            : config('blueprint.namespace');
    }

    private function fullyQualifiedModelClassName(string $name): string
    {
        /** @var Model $modelContext */
        $modelContext = $this->tree->modelForContext($name) ?? new Model($name);

        return $modelContext->fullyQualifiedClassName();
    }
}
