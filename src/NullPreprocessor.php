<?php namespace BapCat\Nom;

class NullPreprocessor implements Preprocessor {
  public function process($code) {
    return $code;
  }
}
