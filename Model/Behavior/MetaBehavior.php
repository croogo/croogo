<?php
/**
 * Meta Behavior
 *
 * PHP version 5
 *
 * @category Behavior
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MetaBehavior extends ModelBehavior {
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
                $customFields = array();
                if (isset($result['Meta']) && count($result['Meta']) > 0) {
                    $customFields = Set::combine($result, 'Meta.{n}.key', 'Meta.{n}.value');
                }
                $results[$i]['CustomFields'] = $customFields;
            }
        } elseif (isset($results[$model->alias])) {
            $customFields = array();
            if (isset($results['Meta']) && count($results['Meta']) > 0) {
                $customFields = Set::combine($results, 'Meta.{n}.key', 'Meta.{n}.value');
            }
            $results['CustomFields'] = $customFields;
        }

        return $results;
    }
/**
 * Prepare data
 *
 * @param object $model
 * @param array  $data
 * @return array
 */
    public function prepareData(&$model, $data) {
        return $this->__prepareMeta($data);
    }
/**
 * Private method for MetaBehavior::prepareData()
 *
 * @param object $model
 * @param array  $data
 * @return array
 */
    private function __prepareMeta($data) {
        if (isset($data['Meta']) &&
            is_array($data['Meta']) &&
            count($data['Meta']) > 0 &&
            !Set::numeric(array_keys($data['Meta']))) {
            $meta = $data['Meta'];
            $data['Meta'] = array();
            $i = 0;
            foreach ($meta AS $metaUuid => $metaArray) {
                $data['Meta'][$i] = $metaArray;
                $i++;
            }
        }

        return $data;
    }
/**
 * Save with meta
 *
 * @param object $model
 * @param array  $data
 * @param array  $options
 * @return void
 */
    public function saveWithMeta(&$model, $data, $options = array()) {
        $data = $this->__prepareMeta($data);
        return $model->saveAll($data, $options);
    }

}
?>