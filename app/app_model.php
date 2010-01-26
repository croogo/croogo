<?php
/**
 * Application model
 *
 * This file is the base model of all other models
 *
 * PHP version 5
 *
 * @category Models
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AppModel extends Model {

/**
 * use Caching
 *
 * @var string
 */
    var $useCache = true;

/**
 * Override find function to use caching
 *
 * @param mixed $type 
 * @param array $options 
 * @return mixed
 * @access public
 */
    function find($type, $options = array()) {
        if ($this->useCache) {
            $cachedResults = $this->_findCached($type, $options);
            if ($cachedResults) {
                return $cachedResults;
            }
        }

        $args = func_get_args();
        $results = call_user_func_array(array('parent', 'find'), $args);
        if (isset($options['cache']['name']) && 
            isset($options['cache']['config']) &&
            $this->useCache) {
            Cache::write($options['cache']['name'], $results, $options['cache']['config']);
        }
        return $results;
    }

/**
 * Check if find() was already cached
 *
 * @param mixed $type 
 * @param array $options 
 * @return void
 * @access private
 */
    function _findCached($type, $options) {
        if (isset($options['cache']['name']) &&
            isset($options['cache']['config'])) {
            $results = Cache::read($options['cache']['name'], $options['cache']['config']);
            if ($results) {
                return $results;
            } else {
                return false;
            }
        }
        return false;
    }
}
?>