<?php

use BapCat\Nom\NullPreprocessor;

class NullPreprocessorTest extends PHPUnit_Framework_TestCase {
  private $preprocessor;
  
  public function setUp() {
    $this->preprocessor = new NullPreprocessor();
  }
  
  public function testProcess() {
    $input  = 'This is a test';
    $output = $this->preprocessor->process($input);
    
    $this->assertSame($input, $output);
  }
}
