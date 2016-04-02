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
    $template = $this->templates->child['valid.php'];
    
    $pipeline = new Pipeline($this->cache, $this->compiler, [$this->mockTransformer(), $this->mockTransformer2()]);
    $compiled = $pipeline->compile($template);
    
    $this->assertSame('test', $compiled);
  }
  
  public function testSinglePostprocessor() {
    $template = $this->templates->child['valid.php'];
    
    $pipeline = new Pipeline($this->cache, $this->compiler, [], [$this->mockTransformer()]);
    $compiled = $pipeline->compile($template);
    
    $this->assertSame('tset', $compiled);
  }
  
  public function testMultiplePostprocessors() {
    $template = $this->templates->child['valid.php'];
    
    $pipeline = new Pipeline($this->cache, $this->compiler, [], [$this->mockTransformer(), $this->mockTransformer2()]);
    $compiled = $pipeline->compile($template);
    
    $this->assertSame('test', $compiled);
  }
  
  public function testSinglePreprocessorAndPostProcessor() {
    $template = $this->templates->child['valid.php'];
    
    $pipeline = new Pipeline($this->cache, $this->compiler, [$this->mockTransformer()], [$this->mockTransformer2()]);
    $compiled = $pipeline->compile($template);
    
    $this->assertSame('test', $compiled);
  }
  
  public function testMultiplePreprocessorsAndPostprocessors() {
    $template = $this->templates->child['valid.php'];
    
    $pipeline = new Pipeline($this->cache, $this->compiler, [$this->mockTransformer(), $this->mockTransformer2()], [$this->mockTransformer(), $this->mockTransformer2()]);
    $compiled = $pipeline->compile($template);
    
    $this->assertSame('test', $compiled);
  }

  /**
   * Mocks out a transformer that reverses the code
   */
  private function mockTransformer() {
    $mock = $this
      ->getMockBuilder(Transformer::class)
      ->setMethods(['transform'])
      ->getMockForAbstractClass()
    ;
    
    $mock
      ->expects($this->any())
      ->method('transform')
      ->will($this->returnCallback(function($code) {
        return str_replace('test', 'tset', $code);
      }))
    ;
    
    return $mock;
  }
  
  /**
   * Mocks out a transformer that reverses the code
   */
  private function mockTransformer2() {
    $mock = $this
      ->getMockBuilder(Transformer::class)
      ->setMethods(['transform'])
      ->getMockForAbstractClass()
    ;
    
    $mock
      ->expects($this->any())
      ->method('transform')
      ->will($this->returnCallback(function($code) {
        return str_replace('tset', 'test', $code);
      }))
    ;
    
    return $mock;
  }
}
