<?php declare(strict_types=1); namespace BapCat\Nom;

use BapCat\Persist\Drivers\Local\LocalFile;

use Error;
use Throwable;

/**
 * Thrown if there is a problem compiling a template
 */
class TemplateCompilationError extends Error {
  /** @var  LocalFile  $template */
  private $template;

  /**
   * @param  LocalFile  $template   The template that exceptioned
   * @param  Throwable  $throwable  The throwable
   */
  public function __construct(LocalFile $template, Throwable $throwable) {
    $this->template  = $template;

    parent::__construct("A problem occurred while compiling [{$template->path}]:\n$throwable", $throwable->getCode(), $throwable);
  }

  /**
   * Accessor for the template file
   *
   * @return  LocalFile  The template
   */
  public function getTemplate(): LocalFile {
    return $this->template;
  }
}
