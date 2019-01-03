<?php declare(strict_types=1); namespace BapCat\Nom;

/* Most of this code is taken from and owned by Illuminate/View */

use BapCat\Persist\Drivers\Local\LocalFile;

use ParseError;
use Throwable;

use function count;

/**
 * Loads and compiles a template file
 */
class Compiler {
  /**
   * Compile a template
   *
   * @param  LocalFile  $_bap_path  The template file
   * @param  array      $_bap_data  Keys are variable names, values values
   *
   * @return  string  The compiled template
   *
   * @throws  TemplateNotFoundException
   */
  public function compile(LocalFile $_bap_path, array $_bap_data = []): string {
    if(!$_bap_path->exists) {
      throw new TemplateNotFoundException($_bap_path);
    }

    $_bap_level = ob_get_level();
    ob_start();

    if(extract($_bap_data, EXTR_SKIP) !== count($_bap_data)) {
      throw new ParseError('Invalid variable name used');
    }

    try {
      include $_bap_path->full_path;
    } catch(Throwable $e) {
      while(ob_get_level() > $_bap_level) {
        ob_end_clean();
      }

      throw new TemplateCompilationError($_bap_path, $e);
    }

    return ltrim(ob_get_clean());
  }
}
