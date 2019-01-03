<?php declare(strict_types=1);

use BapCat\Nom\Compiler;
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

  /**
   * @expectedException BapCat\Nom\TemplateNotFoundException
   */
  public function testCompileTemplateNotFound(): void {
    $path = $this->fs->getFile('bap.nommm');

    $this->compiler->compile($path);
  }

  /**
   * @expectedException BapCat\Nom\TemplateCompilationError
   */
  public function testCompileSyntaxError(): void {
    $path = $this->fs->getFile('invalid.php');

    $this->compiler->compile($path);
  }

  /**
   * @expectedException BapCat\Nom\TemplateCompilationError
   */
  public function testCompileMistake(): void {
    $path = $this->fs->getFile('mistake.php');

    $this->compiler->compile($path);
  }
}
