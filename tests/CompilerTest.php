<?php declare(strict_types=1);

use BapCat\Nom\Compiler;
use BapCat\Nom\TemplateCompilationError;
use BapCat\Nom\TemplateNotFoundException;
use BapCat\Persist\Drivers\Local\LocalDriver;
use PHPUnit\Framework\TestCase;

class CompilerTest extends TestCase {
  /** @var  Compiler  $compiler */
  private $compiler;

  /** @var  LocalDriver  $fs */
  private $fs;

  protected function setUp(): void {
    parent::setUp();
    $this->compiler = new Compiler();
    $this->fs = new LocalDriver(__DIR__ . '/data');
  }

  public function testCompile(): void {
    $path = $this->fs->getFile('bap.nom');

    $compiled = $this->compiler->compile($path);
    $expected = file_get_contents($path->full_path);

    $this->assertEquals($expected, $compiled);
  }

  public function testCompileTemplateNotFound(): void {
    $path = $this->fs->getFile('bap.nommm');

    $this->expectException(TemplateNotFoundException::class);
    $this->compiler->compile($path);
  }

  public function testCompileSyntaxError(): void {
    $path = $this->fs->getFile('invalid.php');

    $this->expectException(TemplateCompilationError::class);
    $this->compiler->compile($path);
  }

  public function testCompileMistake(): void {
    $path = $this->fs->getFile('mistake.php');

    $this->expectException(TemplateCompilationError::class);
    $this->compiler->compile($path);
  }
}
