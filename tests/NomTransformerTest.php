<?php declare(strict_types=1);

use BapCat\Nom\NomTransformer;
use PHPUnit\Framework\TestCase;

class NomTransformerTest extends TestCase {
  /** @var  NomTransformer  $pretransformer */
  private $pretransformer;

  /** @var  string[]  $inflectors */
  private static $inflectors = [
    'titleize',
    'camelize',
    'underscore',
    'pluralize',
    'singularize',
    'humanize',
    'ordinal',
    'ordinalize',
  ];

  public function setUp(): void {
    parent::setUp();
    $this->pretransformer = new NomTransformer();
  }

  public function testTransformPhp(): void {
    $input  = '@php echo;';
    $expected = "<<?= '?php' ?> echo;";
    $this->transform($input, $expected);
  }

  public function testTransformInflector(): void {
    $string = 'bap off';

    foreach(self::$inflectors as $inflector) {
      $input  = "@{$inflector} ( \"$string\" )";
      $expected = "\ICanBoogie\Inflector::get()->{$inflector}(\"$string\")";
      $this->transform($input, $expected);
    }
  }

  public function testEscapedEcho(): void {
    $input = '{{  $bap  }}';
    $expected = '<?= htmlentities($bap) ?>';
    $this->transform($input, $expected);
  }

  public function testUnescapedEcho(): void {
    $input = '{!   $bap   !}';
    $expected = '<?= $bap ?>';
    $this->transform($input, $expected);
  }

  /**
   * @dataProvider  ifProvider
   *
   * @param  string  $input
   * @param  string  $expected
   */
  public function testIf(string $input, string $expected): void {
    $this->transform($input, $expected);
  }

  public function ifProvider(): array {
    return [
      ['@if(blah)', '<?php if(blah): ?>'],
      ['@if   ( blah ) ', '<?php if( blah ): ?> '],
      ['@if($this->blah($this->blahblah())($blah)())', '<?php if($this->blah($this->blahblah())($blah)()): ?>'],
    ];
  }

  /**
   * @dataProvider  elseProvider
   *
   * @param  string  $input
   * @param  string  $expected
   */
  public function testElse(string $input, string $expected): void {
    $this->transform($input, $expected);
  }

  public function elseProvider(): array {
    return [
      ['@else(blah)', '<?php elseif(blah): ?>'],
      ['@else   ( blah ) ', '<?php elseif( blah ): ?> '],
      ['@else($this->blah($this->blahblah())($blah)())', '<?php elseif($this->blah($this->blahblah())($blah)()): ?>'],
      ['@else', '<?php else: ?>'],
    ];
  }

  /**
   * @dataProvider  endIfProvider
   *
   * @param  string  $input
   * @param  string  $expected
   */
  public function testEndIf(string $input, string $expected): void {
    $this->transform($input, $expected);
  }

  public function endIfProvider(): array {
    return [
      ['@endif', '<?php endif; ?>'],
    ];
  }

  /**
   * @dataProvider  forEachProvider
   *
   * @param  string  $input
   * @param  string  $expected
   */
  public function testForEach(string $input, string $expected): void {
    $this->transform($input, $expected);
  }

  public function forEachProvider(): array {
    return [
      ['@each ( bap as $cat )', '<?php foreach(bap as $cat): ?>'],
      ['@each ( $this->bap($blah )( ) as $cat )', '<?php foreach($this->bap($blah )( ) as $cat): ?>'],
      ['@each (  bap   as  $cat,$it   )', '<?php foreach(bap as $cat => $it): ?>'],
      ['@each (  $this->bap($blah )( )   as  $cat,$it   )', '<?php foreach($this->bap($blah )( ) as $cat => $it): ?>'],
    ];
  }

  /**
   * @dataProvider  endForEachProvider
   *
   * @param  string  $input
   * @param  string  $expected
   */
  public function testEndForEach(string $input, string $expected): void {
    $this->transform($input, $expected);
  }

  public function endForEachProvider(): array {
    return [
      ['@endeach', '<?php endforeach; ?>'],
    ];
  }

  private function transform(string $input, string $expected): void {
    $output = $this->pretransformer->transform($input);
    $this->assertSame($output, $expected);
  }
}
