<?php namespace BapCat\Nom;

use BapCat\Values\Regex;

/**
 * Compiles Nom-style templates into standard PHP templates
 */
class NomTransformer implements Transformer {
  private $replacements;
  
  /**
   * Constructor
   */
  public function __construct() {
    $this->replacements = [
      [
        new Regex('/@php/'),
        '<<?= \'?php\' ?>'
      ], [
        new Regex('/@(titleize|camelize|underscore|pluralize|singularize|humanize|ordinal|ordinalize)\s*\(\s*(.+?)\s*\)/'),
        '\\\\ICanBoogie\\\\Inflector::get()->$1($2)'
      ], [
        new Regex('/{{\s*(.+?)\s*}}/'),
        '<?= htmlentities($1) ?>'
      ], [
        new Regex('/{!\s*(.+?)\s*!}/'),
        '<?= $1 ?>'
      ], [
        new Regex('/@if\s*\((?<a>(?:[^()]*(?:(?=\()\((?&a)\s*\)|))+)\s*\)/'),
        '<?php if($1): ?>'
      ], [
        new Regex('/@else\s*\((?<a>(?:[^()]*(?:(?=\()\((?&a)\s*\)|))+)\s*\)/'),
        '<?php elseif($1): ?>'
      ], [
        new Regex('/@else/'),
        '<?php else: ?>'
      ], [
        new Regex('/@endif/'),
        '<?php endif; ?>'
      ], [
        new Regex('/@each\s*\(\s*(.+?)\s+as\s+\$(\w+?)\s*\)/'),
        '<?php foreach($1 as \$$2): ?>'
      ], [
        new Regex('/@each\s*\(\s*(.+?)\s+as\s*\$(\w+?)\s*,\s*\$(\w+?)\s*\)/'),
        '<?php foreach($1 as \$$2 => \$$3): ?>'
      ], [
        new Regex('/@endeach/'),
        '<?php endforeach; ?>'
      ]
    ];
  }
  
  /**
   * {@inheritDoc}
   */
  public function transform($code) {
    foreach($this->replacements as $replacement) {
      $code = preg_replace($replacement[0]->raw, $replacement[1], $code);
    }
    
    return $code;
  }
}
