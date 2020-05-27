<?php

namespace Fidum\BlueprintPestAddon;

use Blueprint\Contracts\Generator;
use Fidum\BlueprintPestAddon\Actions\MakeHttpTests;
use Fidum\BlueprintPestAddon\Actions\MakeExampleFeatureTest;
use Fidum\BlueprintPestAddon\Actions\MakeExampleUnitTest;
use Fidum\BlueprintPestAddon\Actions\MakePestGlobalFile;
use Fidum\BlueprintPestAddon\Contracts\Action as ActionContract;
use Fidum\BlueprintPestAddon\Traits\HasStubFile;

class PestGenerator implements Generator
{
    use HasStubFile;

    /** @var \Illuminate\Contracts\Filesystem\Filesystem */
    private $files;

    public function __construct($files)
    {
        $this->files = $files;
    }

    public function output(array $tree): array
    {
        if (empty($tree['controllers'])) {
            return [];
        }

        $output = [];

        collect([
            new MakePestGlobalFile,
            new MakeExampleFeatureTest,
            new MakeExampleUnitTest,
            new MakeHttpTests,
        ])->each(function (ActionContract $action) use ($tree, &$output) {
            $results = $action->execute($this->files, $tree)->output();
            $output = array_merge_recursive($output, $results);
        });

        return $output;
    }
}
