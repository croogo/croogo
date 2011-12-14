<?php
/**
 * Params Behavior
 *
 * PHP version 5
 *
 * @category Behavior
 * @package  Croogo
 * @since    1.3.1
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ParamsBehavior extends ModelBehavior {
/**
 * Setup
 *
 * @param object $model
 * @param array  $config
 * @return void
 */
    public function setup(&$model, $config = array()) {
        if (is_string($config)) {
            $config = array($config);
        }

        $this->settings[$model->alias] = $config;
    }
/**
 * afterFind callback
 *
 * @param object  $model
 * @param array   $created
 * @param boolean $primary
 * @return array
 */
    public function afterFind(&$model, $results = array(), $primary = false) {
        if ($primary && isset($results[0][$model->alias])) {
            foreach ($results AS $i => $result) {
                $params = array();
                if (isset($result[$model->alias]['params']) && strlen($result[$model->alias]['params']) > 0) {
                    $params = $this->paramsToArray($model, $result[$model->alias]['params']);
                }
                $results[$i]['Params'] = $params;
            }
        } elseif (isset($results[$model->alias])) {
            $params = array();
            if (isset($results[$model->alias]['params']) && strlen($results[$model->alias]['params']) > 0) {
                $params = $this->paramsToArray($model, $results[$model->alias]['params']);
            }
            $results['Params'] = $params;
        }

        return $results;
    }
/**
 * Converts a string of params to an array of formatted key/value pairs
 *
 * String is supposed to have one parameter per line in the format:
 * my_param_key=value_here
 * another_param=another_value
 *
 * @param object $model
 * @param string $params
 * @return array
 */
    public function paramsToArray(&$model, $params) {
        $output = array();
        $params = explode("\n", $params);
        foreach ($params AS $param) {
            if (strlen($param) == 0) {
                continue;
            }

            $paramE = explode('=', $param);
            if (count($paramE) == 2) {
                $key = $paramE['0'];
                $value = $paramE['1'];
                $output[$key] = $value;
            }
        }
        return $output;
    }

}
?>