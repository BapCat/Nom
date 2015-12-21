<?php namespace BapCat\Nom;

use BapCat\Persist\Drivers\Local\LocalFile;

use Exception;
use Throwable;

class TemplateCompilationException extends Exception {
  private $template;
  private $ex;
  
  public function __construct(LocalFile $template, Throwable $ex) {
    $this->template = $template;
    $this->ex       = $ex;
    
    parent::__construct("An exception occurred while compiling [{$template->path}]:\n$ex");
  }
  
  public function getTemplate() {
    return $this->template;
  }
  
  public function getException() {
    return $this->ex;
  }
}
