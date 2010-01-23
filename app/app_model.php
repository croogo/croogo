<?php
class AppModel extends Model {

    var $useCache = true;

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