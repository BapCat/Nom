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
    $_bap_level = ob_get_level();
    ob_start();
    
    extract($_bap_data);
    
    try {
      //GOTCHA: have to compare to `true`, can't use `not`
      if((@include $_bap_path->full_path) != true) {
        throw new TemplateNotFoundException($_bap_path);
      }
    } catch(Exception $e) {
      $this->handleViewException($e, $_bap_level);
    } catch(Throwable $e) {
      // Handle PHP7 throwables
      //@TODO probably don't want to use Exception
      $this->handleViewException(new Exception($e), $_bap_level);
    }
    
    return ltrim(ob_get_clean());
  }
  
  private function handleViewException(Exception $e, $ob_level) {
    while(ob_get_level() > $ob_level) {
      ob_end_clean();
    }
    
    throw $e;
  }
}
