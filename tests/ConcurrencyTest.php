<?php

class ConcurrencyTest extends PHPUnit_Framework_TestCase {
  public function setUp() {
    $file = __DIR__ . '/cache/concurrency-test';
    
    if(file_exists($file)) {
      unlink($file);
    }
  }
  
  public function testConcurrency() {
    for($i = 0; $i < 20; $i++) {
      exec(__DIR__ . '/spawn > /dev/null &');
    }
  }
}
