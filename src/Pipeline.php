<?php namespace BapCat\Nom;

use BapCat\Persist\File;
use BapCat\Persist\Directory;

/**
 * A pipeline for pre-processing, compiling, and post-processing templates
 *
 * Please note that if pre-processing is used, a temporary file must
 * be created.  Applications requiring high performance should skip
 * this step unless caching is used.
 */
class Pipeline {
  /**
   * @var  Directory
   */
  private $cache;
  
  /**
   * @var  Compiler
   */
  private $compiler;
  
  /**
   * @var  array<Transformer>
   */
  private $preprocessors;
  
  /**
   * @var  array<Transformer>
   */
  private $postprocessors;
  
  /**
   * Constructor
   *
   * @var  Directory           $cache           A temporary directory used during pre-processing
   * @var  Compiler            $compiler        The class being used to compile templates
   * @var  array<Transformer>  $preprocessors   (optional) An array of pre-processors to execute sequentially before compilation
   * @var  array<Transformer>  $postprocessors  (optional) An array of post-processors to execute sequentially after compilation
   */
  public function __construct(Directory $cache, Compiler $compiler, array $preprocessors = [], array $postprocessors = []) {
    $this->cache          = $cache;
    $this->compiler       = $compiler;
    $this->preprocessors  = $preprocessors;
    $this->postprocessors = $postprocessors;
  }
  
  /**
   * Executes pre-processors, compiler, and post-processors on a template
   *
   * @param  File          $template       The template to send through the pipeline
   * @param  array<mixed>  $template_vars  A keyed array of variables to pass into the template.  All variables
   *                                       will be accessible in the template by their key (eg. `$array_key`)
   *
   * @return  string  The processed and compiled template
   */
  public function compile(File $template, array $template_vars = []) {
    $template = $template->makeLocal();
    
    if(!empty($this->preprocessors)) {
      $processed_code = $template->readAll();
      
      foreach($this->preprocessors as $preprocessor) {
        $processed_code = $preprocessor->transform($processed_code);
      }
      
      $template = $this->cache->child[$template->name];
      $template->writeAll($processed_code);
    }
    
    $output = $this->compiler->compile($template, $template_vars);
    
    foreach($this->postprocessors as $postprocessor) {
      $output = $postprocessor->transform($output);
    }
    
    return $output;
  }
}
