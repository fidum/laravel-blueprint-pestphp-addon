<?php

namespace Fidum\BlueprintPestAddon\Builders\Concerns;

trait BuildsFactoryStatements
{
    private function classFactory(string $variable, string $model, int $count): string
    {
        $times = $count > 1 ? sprintf('->times(%s)', $count) : '';

        return sprintf('$%s = %s::factory()%s->create();', $variable, $model, $times);
    }

    private function legacyFactory(string $variable, string $model, int $count): string
    {
        $times = $count > 1 ? sprintf(', %s', $count) : '';

        return sprintf('$%s = factory(%s::class%s)->create();', $variable, $model, $times);
    }
}
