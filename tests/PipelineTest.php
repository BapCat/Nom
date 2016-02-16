<?php

use BapCat\Nom\Pipeline;
use BapCat\Nom\Compiler;
use BapCat\Nom\Transformer;
use BapCat\Persist\Drivers\Local\LocalDriver;

class PipelineTest extends PHPUnit_Framework_TestCase {
  private $cache;
  private $templates;
  private $compiler;
  
  public function setUp() {
    $fs = new LocalDriver(__DIR__);
    
    $this->cache     = $fs->getDirectory('/cache');
    $this->templates = $fs->getDirectory('/data');
    
    $this->compiler = new Compiler();
  }
  
  public function testNoTransformers() {
    $template = $this->templates->child['valid.php'];
    
    $pipeline = new Pipeline($this->cache, $this->compiler);
    $compiled = $pipeline->compile($template);
    
    $this->assertSame('test', $compiled);
  }
  
  public function testSinglePreprocessor() {
    $template = $this->templates->child['valid.php'];
    
    $pipeline = new Pipeline($this->cache, $this->compiler, [$this->mockTransformer()]);
    $compiled = $pipeline->compile($template);
    
    $this->assertSame('tset', $compiled);
  }
  
  public function testMultiplePreprocessors() {
    
  }
  
  public function testSinglePostprocessor() {
    
  }
  
  public function testMultiplePostprocessors() {
    
  }
  
  public function testSinglePreprocessorAndPostProcessor() {
    
  }
  
  public function testMultiplePreprocessorsAndPostprocessors() {
    
  }
  
  /**
   * Mocks out a transformer that reverses the code
   */
  private function mockTransformer() {
    $mock = $this
      ->getMockBuilder(Transformer::class)
      ->setMethods(['process'])
      ->getMockForAbstractClass()
    ;
    
    $mock
      ->expects($this->any())
      ->method('process')
      ->will($this->returnCallback(function($code) {
        return str_replace('test', 'tset', $code);
      }))
    ;
    
    return $mock;
  }
}
