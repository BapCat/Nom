<?php declare(strict_types=1); namespace BapCat\Nom;

use BapCat\Persist\Drivers\Local\LocalFile;
use BapCat\Persist\File;
use BapCat\Persist\Directory;
use RuntimeException;

/**
 * A pipeline for pre-processing, compiling, and post-processing templates
 *
 * Please note that if pre-processing is used, a temporary file must
 * be created.  Applications requiring high performance should skip
 * this step unless caching is used.
 */
class Pipeline {
  /** @var  Directory  $cache */
  private $cache;

  /** @var  Compiler  $compiler */
  private $compiler;

  /** @var  Transformer[]  $preprocessors */
  private $preprocessors;

  /** @var  Transformer[]  $postprocessors */
  private $postprocessors;

  /**
   * @var  Directory      $cache           A temporary directory used during pre-processing
   * @var  Compiler       $compiler        The class being used to compile templates
   * @var  Transformer[]  $preprocessors   (optional) An array of pre-processors to execute sequentially before compilation
   * @var  Transformer[]  $postprocessors  (optional) An array of post-processors to execute sequentially after compilation
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
   * @param  File    $template        The template to send through the pipeline
   * @param  mixed[] $template_vars   A keyed array of variables to pass into the template.  All variables
   *                                  will be accessible in the template by their key (eg. `$array_key`)
   *
   * @return  string  The processed and compiled template
   *
   * @throws  TemplateNotFoundException
   */
  public function compile(File $template, array $template_vars = []): string {
    $template = $template->makeLocal();

    if(!empty($this->preprocessors)) {
      $processed_code = $template->readAll();

      foreach($this->preprocessors as $preprocessor) {
        $processed_code = $preprocessor->transform($processed_code);
      }

      $template = $this->cache->child[$template->name];

      if(!($template instanceof LocalFile)) {
        throw new RuntimeException('Template cache is not a file');
      }

      $template->writeAll($processed_code);
    }

    $output = $this->compiler->compile($template, $template_vars);

    foreach($this->postprocessors as $postprocessor) {
      $output = $postprocessor->transform($output);
    }

    return $output;
  }
}
