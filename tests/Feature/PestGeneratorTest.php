<?php

namespace Fidum\BlueprintPestAddon\Tests\Feature;

use Blueprint\Blueprint;
use Blueprint\Lexers\ControllerLexer;
use Blueprint\Lexers\ModelLexer;
use Fidum\BlueprintPestAddon\PestGenerator;
use Fidum\BlueprintPestAddon\Tests\TestCase;
use Mockery\MockInterface;

class PestGeneratorTest extends TestCase
{
    /** @var Blueprint */
    public $blueprint;

    /** @var MockInterface  */
    public $files;

    /** @var PestGenerator */
    public $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = \Mockery::mock();

        $this->subject = new PestGenerator($this->files);

        $this->blueprint = new Blueprint();
        $this->blueprint->registerLexer(new ModelLexer());
        $this->blueprint->registerLexer(new ControllerLexer(new \Blueprint\Lexers\StatementLexer()));
        $this->blueprint->registerGenerator($this->subject);
    }

    public function testNothingGeneratedWithEmptyTree()
    {
        $this->files->expects('put')->never();

        /** @var Blueprint $blueprint */
        $this->assertSame([], $this->subject->output([]));
    }

    public function testNothingGeneratedWithEmptyModelsTree()
    {
        $this->files->expects('put')->never();

        /** @var Blueprint $blueprint */
        $this->assertSame([], $this->subject->output(['models' => []]));
    }

    public function testPestFileGenerated()
    {
        $pestFile = 'tests/Pest.php';
        $this->files->expects('put')->with($pestFile, $this->fixture($pestFile));

        /** @var Blueprint $blueprint */
        $tokens = $this->blueprint->parse($this->definition());
        $tree = $this->blueprint->analyze($tokens);

        $this->assertSame(['created' => [$pestFile]], $this->subject->output($tree));
    }
}
