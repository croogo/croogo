<?php
/**
 * Thread Helper
 *
 * PHP version 5
 *
 * @category Helper
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ThreadHelper extends AppHelper {
/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
    var $helpers = array('Html');
/**
 * Get an array from threaded model output with spaced titles as keys
 *
 * @param array $items threaded output from model
 * @param string $classField Term.title, Node.title, etc
 * @param string $spacer (optional) character(s) to be placed before children
 * @param integer $depth (optional) depth level
 * @return array
 */
    function getList($items, $classicField, $spacer = '_', $depth = 0) {
        $classicFieldE = explode('.', $classicField);
        $model = $classicFieldE['0'];
        $field = $classicFieldE['1'];

        $output = array();
        foreach ($items AS $key => $item) {
            $spacedKey = $this->spacerByDepth($spacer, $depth) . $item[$model][$field];
            if (count($item['children']) > 0) {
                $item[$model]['spacedKey'] = $spacedKey;
                $output[$spacedKey] = $item;
                $output = array_merge($output, $this->getList($item['children'], $classicField, $spacer, $depth + 1));
            } else {
                $item[$model]['spacedKey'] = $spacedKey;
                $output[$spacedKey] = $item;
            }
        }
        return $output;
    }
/**
 * Get spacer (prefix)
 *
 * @param string $spacer spacer
 * @param integer $depth depth level
 * @return string
 */
    function spacerByDepth($spacer, $depth = 0) {
        $output = '';
        for ($i = 0; $i < $depth;) {
            $output .= $spacer;
            $i++;
        }
        return $output;
    }

}
?>