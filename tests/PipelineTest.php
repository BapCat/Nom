<?php declare(strict_types=1);

use BapCat\Nom\Pipeline;
use BapCat\Nom\Compiler;
use BapCat\Nom\Transformer;
use BapCat\Persist\Drivers\Local\LocalDirectory;
use BapCat\Persist\Drivers\Local\LocalDriver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PipelineTest extends TestCase {
  /** @var  LocalDirectory  $cache */
  private $cache;

  /** @var  LocalDirectory  $templates */
  private $templates;

  /** @var  Compiler  $compiler */
  private $compiler;

  public function setUp(): void {
    parent::setUp();

    $fs = new LocalDriver(__DIR__);

    $this->cache     = $fs->getDirectory('/cache');
    $this->templates = $fs->getDirectory('/data');

    $this->compiler = new Compiler();
  }

  public function testNoTransformers(): void {
    $template = $this->templates->child['valid.php'];

    $pipeline = new Pipeline($this->cache, $this->compiler);
    $compiled = $pipeline->compile($template);

    $this->assertSame('test', $compiled);
  }

  public function testSinglePreprocessor(): void {
    $template = $this->templates->child['valid.php'];

    $pipeline = new Pipeline($this->cache, $this->compiler, [$this->mockTransformer()]);
    $compiled = $pipeline->compile($template);

    $this->assertSame('tset', $compiled);
  }

  public function testMultiplePreprocessors(): void {
    $template = $this->templates->child['valid.php'];

    $pipeline = new Pipeline($this->cache, $this->compiler, [$this->mockTransformer(), $this->mockTransformer2()]);
    $compiled = $pipeline->compile($template);

    $this->assertSame('test', $compiled);
  }

  public function testSinglePostprocessor(): void {
    $template = $this->templates->child['valid.php'];

    $pipeline = new Pipeline($this->cache, $this->compiler, [], [$this->mockTransformer()]);
    $compiled = $pipeline->compile($template);

    $this->assertSame('tset', $compiled);
  }

  public function testMultiplePostprocessors(): void {
    $template = $this->templates->child['valid.php'];

    $pipeline = new Pipeline($this->cache, $this->compiler, [], [$this->mockTransformer(), $this->mockTransformer2()]);
    $compiled = $pipeline->compile($template);

    $this->assertSame('test', $compiled);
  }

  public function testSinglePreprocessorAndPostProcessor(): void {
    $template = $this->templates->child['valid.php'];

    $pipeline = new Pipeline($this->cache, $this->compiler, [$this->mockTransformer()], [$this->mockTransformer2()]);
    $compiled = $pipeline->compile($template);

    $this->assertSame('test', $compiled);
  }

  public function testMultiplePreprocessorsAndPostprocessors(): void {
    $template = $this->templates->child['valid.php'];

    $pipeline = new Pipeline($this->cache, $this->compiler, [$this->mockTransformer(), $this->mockTransformer2()], [$this->mockTransformer(), $this->mockTransformer2()]);
    $compiled = $pipeline->compile($template);

    $this->assertSame('test', $compiled);
  }

  /**
   * Mocks out a transformer that reverses the code
   */
  private function mockTransformer(): MockObject {
    $mock = $this
      ->getMockBuilder(Transformer::class)
      ->setMethods(['transform'])
      ->getMockForAbstractClass()
    ;

    $mock
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
  private function mockTransformer2(): MockObject {
    $mock = $this
      ->getMockBuilder(Transformer::class)
      ->setMethods(['transform'])
      ->getMockForAbstractClass()
    ;

    $mock
      ->method('transform')
      ->will($this->returnCallback(function($code) {
        return str_replace('tset', 'test', $code);
      }))
    ;

    return $mock;
  }
}
