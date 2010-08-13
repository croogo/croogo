<?php
/**
 * Example Behavior
 *
 * PHP version 5
 *
 * @category Behavior
 * @package  Croogo
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExampleBehavior extends ModelBehavior {
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
                if (isset($results[$i][$model->alias]['body'])) {
                    $results[$i][$model->alias]['body'] .= '<p>[Modified by ExampleBehavior]</p>';
                }
            }
        } elseif (isset($results[$model->alias])) {
            if (isset($results[$model->alias]['body'])) {
                $results[$model->alias]['body'] .= '<p>[Modified by ExampleBehavior]</p>';
            }
        }

        return $results;
    }

}
?>