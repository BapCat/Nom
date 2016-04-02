<?php namespace BapCat\Nom;

use BapCat\Persist\Drivers\Local\LocalFile;

use Exception;

/**
 * Thrown if a template was not found
 */
class TemplateNotFoundException extends Exception {
  /**
   * The template file
   *
   * @var LocalFile
   */
  private $template;
  
  /**
   * Constructor
   *
   * @param  LocalFile  $template  The template file
   */
  public function __construct(LocalFile $template) {
    $this->template = $template;
    
    parent::__construct("Template [{$template->path}] could not be found");
  }
  
  /**
   * Accessor for the template file
   *
   * @return  LocalFile
   */
  public function getTemplate() {
    return $this->template;
  }
}
