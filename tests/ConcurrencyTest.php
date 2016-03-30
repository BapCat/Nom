<?php

class ConcurrencyTest extends PHPUnit_Framework_TestCase {
  public function setUp() {
    foreach([__DIR__ . '/cache/concurrency-out', __DIR__ . '/cache/valid.php'] as $file) {
      if(file_exists($file)) {
        unlink($file);
      }
    }
  }
  
  public function testConcurrency() {
    for($i = 0; $i < 2; $i++) {
      exec(__DIR__ . '/spawn > /dev/null &');
    }
  }
}
