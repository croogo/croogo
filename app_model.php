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
    public $useCache = true;
/**
 * Override find function to use caching
 *
 * @param mixed $type 
 * @param array $options 
 * @return mixed
 * @access public
 */
    public function find($type, $options = array()) {
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
    protected function _findCached($type, $options) {
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
/**
 * Updates multiple model records based on a set of conditions.
 *
 * call afterSave() callback after successful update.
 *
 * @param array $fields     Set of fields and values, indexed by fields.
 *                          Fields are treated as SQL snippets, to insert literal values manually escape your data.
 * @param mixed $conditions Conditions to match, true for all records
 * @return boolean True on success, false on failure
 * @access public
 */
    public function updateAll($fields, $conditions = true) {
        $args = func_get_args();
        $output = call_user_func_array(array('parent', 'updateAll'), $args);
        if ($output) {
            $created = false;
            $options = array();
            $this->Behaviors->trigger($this, 'afterSave', array(
                $created,
                $options,
            ));
            $this->afterSave($created);
            $this->_clearCache();
            return true;
        }
        return false;
    }
}
?>