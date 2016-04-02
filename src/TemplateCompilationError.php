<?php namespace BapCat\Nom;

use BapCat\Persist\Drivers\Local\LocalFile;

use Error;
use Throwable;

/**
 * Thrown if there is a problem compiling a template
 */
class TemplateCompilationError extends Error {
  /**
   * The template file
   *
   * @var LocalFile
   */
  private $template;
  
  /**
   * The Throwable that was originally thrown
   *
   * @var Throwable
   */
  private $throwable;
  
  /**
   * Constructor
   *
   * @param  LocalFile  $template   The template that exceptioned
   * @param  Throwable  $throwable  The throwable
   */
  public function __construct(LocalFile $template, Throwable $throwable) {
    $this->template  = $template;
    $this->throwable = $throwable;
    
    parent::__construct("A problem occurred while compiling [{$template->path}]:\n$throwable");
  }
  
  /**
   * Accessor for the template file
   *
   * @return  LocalFile  The template
   */
  public function getTemplate() {
    return $this->template;
  }
  
  /**
   * Accessor for the throwable
   *
   * @return  Throwable  The throwable
   */
  public function getThrowable() {
    return $this->throwable;
  }
}
