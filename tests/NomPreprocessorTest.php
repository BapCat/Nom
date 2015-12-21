<?php

use BapCat\Nom\NomPreprocessor;

class NomPreprocessorTest extends PHPUnit_Framework_TestCase {
  private $preprocessor;
  
  public function setUp() {
    $this->preprocessor = new NomPreprocessor();
  }
  
  public function testProcessPhp() {
    $input  = '@php echo;';
    $expected = "<<?= '?php' ?> echo;";
    $this->process($input, $expected);
  }
  
  public function testProcessInflector() {
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
      $this->process($input, $expected);
    }
  }
  
  public function testEscapedEcho() {
    $input = '{{  $bap  }}';
    $expected = '<?= htmlentities($bap) ?>';
    $this->process($input, $expected);
  }
  
  public function testUnescapedEcho() {
    $input = '{!   $bap   !}';
    $expected = '<?= $bap ?>';
    $this->process($input, $expected);
  }
  
  public function testIf() {
    $input = '@if ( blah )';
    $expected = '<?php if(blah): ?>';
    $this->process($input, $expected);
  }
  
  public function testElseIf() {
    $input = '@else ( blah )';
    $expected = '<?php elseif(blah): ?>';
    $this->process($input, $expected);
  }

  public function testElse() {
    $input = '@else';
    $expected = '<?php else: ?>';
    $this->process($input, $expected);
  }
  
  public function testEndIf() {
    $input = '@endif';
    $expected = '<?php endif; ?>';
    $this->process($input, $expected);
  }
  
  public function testForeach() {
    $input = '@each ( bap as $cat )';
    $expected = '<?php foreach(bap as $cat): ?>';
    $this->process($input, $expected);
  }
  
  public function testForeachWithKey() {
    $input = '@each (  bap   as  $cat,it   )';
    $expected = '<?php foreach(bap as $cat => $it): ?>';
    $this->process($input, $expected);
  }
  
  public function testEndForeach() {
    $input = '@endeach';
    $expected = '<?php endforeach; ?>';
    $this->process($input, $expected);
  }
  
  private function process($input, $expected) {
    $output = $this->preprocessor->process($input);
    $this->assertSame($output, $expected);
  }
}
