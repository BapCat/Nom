<?php

use BapCat\Nom\NomTransformer;

class NomTransformerTest extends PHPUnit_Framework_TestCase {
  private $pretransformer;
  
  private $inflectors = [
    'titleize',
    'camelize',
    'underscore',
    'pluralize',
    'singularize',
    'humanize',
    'ordinal',
    'ordinalize',
  ];
  
  public function setUp() {
    $this->pretransformer = new NomTransformer();
  }
  
  public function testTransformPhp() {
    $input  = '@php echo;';
    $expected = "<<?= '?php' ?> echo;";
    $this->transform($input, $expected);
  }
  
  public function testTransformInflector() {
    $string = "bap off";
    
    foreach($this->inflectors as $inflector) {
      $input  = "@{$inflector} ( \"$string\" )";
      $expected = "\ICanBoogie\Inflector::get()->{$inflector}(\"$string\")";
      $this->transform($input, $expected);
    }
  }
  
  public function testEscapedEcho() {
    $input = '{{  $bap  }}';
    $expected = '<?= htmlentities($bap) ?>';
    $this->transform($input, $expected);
  }
  
  public function testUnescapedEcho() {
    $input = '{!   $bap   !}';
    $expected = '<?= $bap ?>';
    $this->transform($input, $expected);
  }
  
  /**
   * @dataProvider  ifProvider
   */
  public function testIf($input, $expected) {
    $this->transform($input, $expected);
  }
  
  public function ifProvider() {
    return [
      ['@if(blah)', '<?php if(blah): ?>'],
      ['@if   ( blah ) ', '<?php if( blah ): ?> '],
      ['@if($this->blah($this->blahblah())($blah)())', '<?php if($this->blah($this->blahblah())($blah)()): ?>'],
    ];
  }
  
  /**
   * @dataProvider  elseProvider
   */
  public function testElse($input, $expected) {
    $this->transform($input, $expected);
  }
  
  public function elseProvider() {
    return [
      ['@else(blah)', '<?php elseif(blah): ?>'],
      ['@else   ( blah ) ', '<?php elseif( blah ): ?> '],
      ['@else($this->blah($this->blahblah())($blah)())', '<?php elseif($this->blah($this->blahblah())($blah)()): ?>'],
      ['@else', '<?php else: ?>'],
    ];
  }
  
  /**
   * @dataProvider  endIfProvider
   */
  public function testEndIf($input, $expected) {
    $this->transform($input, $expected);
  }
  
  public function endIfProvider() {
    return [
      ['@endif', '<?php endif; ?>'],
    ];
  }
  
  /**
   * @dataProvider  forEachProvider
   */
  public function testForEach($input, $expected) {
    $this->transform($input, $expected);
  }
  
  public function forEachProvider() {
    return [
      ['@each ( bap as $cat )', '<?php foreach(bap as $cat): ?>'],
      ['@each ( $this->bap($blah )( ) as $cat )', '<?php foreach($this->bap($blah )( ) as $cat): ?>'],
      ['@each (  bap   as  $cat,it   )', '<?php foreach(bap as $cat => $it): ?>'],
      ['@each (  $this->bap($blah )( )   as  $cat,it   )', '<?php foreach($this->bap($blah )( ) as $cat => $it): ?>'],
    ];
  }
  
  /**
   * @dataProvider  endForEachProvider
   */
  public function testEndForEach($input, $expected) {
    $this->transform($input, $expected);
  }
  
  public function endForEachProvider() {
    return [
      ['@endeach', '<?php endforeach; ?>'],
    ];
  }
  
  private function transform($input, $expected) {
    $output = $this->pretransformer->transform($input);
    $this->assertSame($output, $expected);
  }
}
