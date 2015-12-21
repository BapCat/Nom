<?php

use BapCat\Nom\Compiler;
use BapCat\Nom\TemplateCompilationException;
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
  
  public function testCompileTemplateNotFound() {
    $this->setExpectedException(TemplateNotFoundException::class);
    
    $path = $this->fs->getFile('bap.nommm');
    
    $compiled = $this->compiler->compile($path);
  }
  
  public function testCompileSyntaxError() {
    // Only run on PHP7
    if(PHP_VERSION_ID < 70000) {
      return;
    }
    
    $this->setExpectedException(TemplateCompilationException::class);
    
    $path = $this->fs->getFile('invalid.php');
    
    $compiled = $this->compiler->compile($path);
  }
  
  public function testCompileMistake() {
    // Only run on PHP7
    if(PHP_VERSION_ID < 70000) {
      return;
    }
    
    $this->setExpectedException(TemplateCompilationException::class);
    
    $path = $this->fs->getFile('mistake.php');
    
    $compiled = $this->compiler->compile($path);
  }
}
