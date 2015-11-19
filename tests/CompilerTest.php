<?php

use BapCat\Nom\Compiler;
use BapCat\Nom\TemplateNotFoundException;
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
  
  public function testCompileException() {
    $this->setExpectedException(TemplateNotFoundException::class);
    
    $path = $this->fs->getFile('bap.nommm');
    
    $compiled = $this->compiler->compile($path);
  }
  
}
