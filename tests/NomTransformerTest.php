<?php

use BapCat\Nom\NomTransformer;

class NomTransformerTest extends PHPUnit_Framework_TestCase {
  private $pretransformor;
  
  public function setUp() {
    $this->pretransformor = new NomTransformer();
  }
  
  public function testTransformPhp() {
    $input  = '@php echo;';
    $expected = "<<?= '?php' ?> echo;";
    $this->transform($input, $expected);
  }
  
  public function testTransformInflector() {
    $inflectors = [
      'titleize',
      'camelize',
      'underscore',
      'pluralize',
      'singularize',
      'humanize',
      'ordinal',
      'ordinalize',
    ];
    
    $string = "bap off";
    
    foreach($inflectors as $inflector) {
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
  
  public function testIf() {
    $input = '@if ( blah )';
    $expected = '<?php if(blah): ?>';
    $this->transform($input, $expected);
  }
  
  public function testElseIf() {
    $input = '@else ( blah )';
    $expected = '<?php elseif(blah): ?>';
    $this->transform($input, $expected);
  }

  public function testElse() {
    $input = '@else';
    $expected = '<?php else: ?>';
    $this->transform($input, $expected);
  }
  
  public function testEndIf() {
    $input = '@endif';
    $expected = '<?php endif; ?>';
    $this->transform($input, $expected);
  }
  
  public function testForeach() {
    $input = '@each ( bap as $cat )';
    $expected = '<?php foreach(bap as $cat): ?>';
    $this->transform($input, $expected);
  }
  
  public function testForeachWithKey() {
    $input = '@each (  bap   as  $cat,it   )';
    $expected = '<?php foreach(bap as $cat => $it): ?>';
    $this->transform($input, $expected);
  }
  
  public function testEndForeach() {
    $input = '@endeach';
    $expected = '<?php endforeach; ?>';
    $this->transform($input, $expected);
  }
  
  private function transform($input, $expected) {
    $output = $this->pretransformor->transform($input);
    $this->assertSame($output, $expected);
  }
}
