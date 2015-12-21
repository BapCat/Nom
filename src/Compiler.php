<?php namespace BapCat\Nom;

/* Most of this code is taken from and owned by Illuminate/View */

use BapCat\Persist\Drivers\Local\LocalFile;

use Exception;
use Throwable;

class Compiler {

  /**
   * Compile a template
   *
   * @param  LocalFile  The template file
   * @param  array      Keys are variable names, values values
   *
   * @return string
   */
  public function compile(LocalFile $_bap_path, array $_bap_data = []) {
    if(!$_bap_path->exists) {
      throw new TemplateNotFoundException($_bap_path);
    }
    
    $_bap_level = ob_get_level();
    ob_start();
    
    extract($_bap_data);
    
    try {
      include $_bap_path->full_path;
    } catch(Exception $e) {
      $this->handleViewException($_bap_path, $e, $_bap_level);
    } catch(Throwable $e) {
      // Handle PHP7 throwables
      //@TODO probably don't want to use Exception
      $this->handleViewException($_bap_path, new Exception($e), $_bap_level);
    }
    
    return ltrim(ob_get_clean());
  }
  
  private function handleViewException(LocalFile $file, Exception $e, $ob_level) {
    while(ob_get_level() > $ob_level) {
      ob_end_clean();
    }
    
    throw new TemplateCompilationException($file, $e);
  }
}
