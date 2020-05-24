<?php

namespace Fidum\BlueprintPestAddon;

use Blueprint\Contracts\Generator;
use Illuminate\Support\Collection;

class PestGenerator implements Generator
{
    use HasStubFilePath;

    /** @var \Illuminate\Contracts\Filesystem\Filesystem */
    private $files;

    public function __construct($files)
    {
        $this->files = $files;
    }

    public function output(array $tree): array
    {
        if (empty($tree['models'])) {
            return [];
        }

        $created = new Collection();

        $this->files->put('tests/Pest.php', file_get_contents($this->stubFilePath('pest.stub')));
        $created->push('tests/Pest.php');

        return ['created' => $created->toArray()];
    }
}
