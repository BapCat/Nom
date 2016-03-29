<?php namespace BapCat\Nom;

use BapCat\Persist\Drivers\Local\LocalFile;

use Exception;

/**
 * Thrown if there is a problem compiling a template
 */
class TemplateCompilationException extends Exception {
  /**
   * The template file
   *
   * @var LocalFile
   */
  private $template;
  
  /**
   * The Exception that was originally thrown
   *
   * @var Exception
   */
  private $ex;
  
  /**
   * Constructor
   *
   * @param  LocalFile  $template  The template that exceptioned
   * @param  Exception  $ex        The exception
   */
  public function __construct(LocalFile $template, Exception $ex) {
    $this->template = $template;
    $this->ex       = $ex;
    
    parent::__construct("An exception occurred while compiling [{$template->path}]:\n$ex");
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
   * Accessor for the exception
   *
   * @return  Exception  The exception
   */
  public function getException() {
    return $this->ex;
  }
}
