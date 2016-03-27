<?php

use BapCat\Nom\Compiler;
use BapCat\Persist\Drivers\Local\LocalDriver;

class CompilerTest extends PHPUnit_Framework_TestCase {
  private $compiler;
  private $fs;
  
  protected function setUp() {
    $this->compiler = new Compiler();
    $this->fs = new LocalDriver(__DIR__ . '/data');
  }
  
  public function testCompile() {
    $path = $this->fs->getFile('bap.nom');
    
    $compiled = $this->compiler->compile($path);
    $expected = file_get_contents($path->full_path);
    
    $this->assertEquals($expected, $compiled);
  }
  
  /**
   * @expectedException BapCat\Nom\TemplateNotFoundException
   */
  public function testCompileTemplateNotFound() {
    $path = $this->fs->getFile('bap.nommm');
    
    $this->compiler->compile($path);
  }
  
  /**
   * @requires PHP 7
   * @expectedException BapCat\Nom\TemplateCompilationException
   */
  public function testCompileSyntaxError() {
    $path = $this->fs->getFile('invalid.php');
    
    $this->compiler->compile($path);
  }
  
  /**
   * @requires PHP 7
   * @expectedException BapCat\Nom\TemplateCompilationException
   */
  public function testCompileMistake() {
    $path = $this->fs->getFile('mistake.php');
    
    $this->compiler->compile($path);
  }
}
