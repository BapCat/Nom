<?php namespace BapCat\Nom;

use BapCat\Persist\Drivers\Local\LocalFile;

use Exception;

class TemplateNotFoundException extends Exception {
  private $template;
  
  public function __construct(LocalFile $template) {
    $this->template = $template;
    
    parent::__construct("Template [{$template->path}] could not be found");
  }
  
  public function getTemplate() {
    return $this->template;
  }
}
