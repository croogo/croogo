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
 * Constructor
 *
 * @param mixed  $id    Set this ID for this model on startup, can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds    DataSource connection name.
 */
    public function __construct($id = false, $table = null, $ds = null) {
        Croogo::applyHookProperties('Hook.model_properties');
        parent::__construct($id, $table, $ds);
    }
/**
 * Override find function to use caching
 *
 * Caching can be done either by unique names,
 * or prefixes where a hashed value of $options array is appended to the name
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
        if ($this->useCache) {
            if (isset($options['cache']['name']) && isset($options['cache']['config'])) {
                $cacheName = $options['cache']['name'];
            } elseif (isset($options['cache']['prefix']) && isset($options['cache']['config'])) {
                $cacheName = $options['cache']['prefix'] . md5(serialize($options));
            }

            if (isset($cacheName)) {
                $cacheName .= '_' . Configure::read('Config.language');
                Cache::write($cacheName, $results, $options['cache']['config']);
            }
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
        if (isset($options['cache']['name']) && isset($options['cache']['config'])) {
            $cacheName = $options['cache']['name'];
        } elseif (isset($options['cache']['prefix']) && isset($options['cache']['config'])) {
            $cacheName = $options['cache']['prefix'] . md5(serialize($options));
        } else {
            return false;
        }

        $cacheName .= '_' . Configure::read('Config.language');
        $results = Cache::read($cacheName, $options['cache']['config']);
        if ($results) {
            return $results;
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