<?php namespace BapCat\Nom;

use BapCat\Persist\File;
use BapCat\Persist\FileReader;

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
  private $templates;
  
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
   * @var  Directory           $templates       The template directory
   * @var  Directory           $cache           A temporary directory used during pre-processing
   * @var  Compiler            $compiler        The class being used to compile templates
   * @var  array<Transformer>  $preprocessors   (optional) An array of pre-processors to execute sequentially before compilation
   * @var  array<Transformer>  $postprocessors  (optional) An array of post-processors to execute sequentially after compilation
   */
  public function __construct(Directory $templates, Directory $cache, Compiler $compiler, array $preprocessors = [], array $postprocessors = []) {
    $this->templates      = $templates;
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
    if(count($this->preprocessors) != 0) {
      $code = null;
      $template->read(function(FileReader $reader) use(&$code) {
        $code = $reader->read();
      });
      
      $processed_code = $code;
      
      foreach($this->preprocessors as $preprocessor) {
        $processed_code = $preprocessor->process($processed_code);
      }
      
      $template = $this->cache->child[$template->name];
      
      //@TODO: use FileWriter when available
      file_put_contents($template->full_path, $processed_code);
    }
    
    $output = $this->compiler->compile($template->makeLocal(), $template_vars);
    
    if(count($this->postprocessors) != 0) {
      foreach($this->postprocessors as $postprocessor) {
        $output = $postprocessor->process($output);
      }
    }
  }
}
