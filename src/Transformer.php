<?php namespace BapCat\Nom;

/**
 * Defines a pre- or post-processor to transform code
 */
interface Transformer {
  /**
   * Transforms code
   *
   * @param  string  $code  The code to transform
   *
   * @return string  The transformed code
   */
  public function transform($code);
}
