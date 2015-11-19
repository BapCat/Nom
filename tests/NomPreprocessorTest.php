<?php

use BapCat\Nom\NomPreprocessor;

class NomPreprocessorTest extends PHPUnit_Framework_TestCase {
  private $preprocessor;
  
  public function setUp() {
    $this->preprocessor = new NomPreprocessor();
  }
  
  public function testProcess() {
    $input  = 'This is a test';
    $output = $this->preprocessor->process($input);
    
    $this->assertSame($input, $output);
  }
}
