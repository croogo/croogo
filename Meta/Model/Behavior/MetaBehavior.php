<?php

App::uses('ModelBehavior', 'Model');

/**
 * Meta Behavior
 *
 * @category Behavior
 * @package  Croogo.Meta.Model.Behavior
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MetaBehavior extends ModelBehavior {

/**
 * Setup
 *
 * @param Model $model
 * @param array $config
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		if (is_string($config)) {
			$config = array($config);
		}

		$this->settings[$model->alias] = $config;

		$model->bindModel(array(
			'hasMany' => array(
				'Meta' => array(
					'className' => 'Meta.Meta',
					'foreignKey' => 'foreign_key',
					'dependent' => true,
					'conditions' => array(
						'Meta.model' => $model->alias,
					),
					'order' => 'Meta.key ASC',
				),
			),
		), false);

		$callback = array($this, 'onBeforeSaveNode');
		$eventManager = $model->getEventManager();
		$eventManager->attach($callback, 'Model.Node.beforeSaveNode');
	}

/**
 * afterFind callback
 *
 * @param Model $model
 * @param array $created
 * @param boolean $primary
 * @return array
 */
	public function afterFind(Model $model, $results, $primary = false) {
		if ($primary && isset($results[0][$model->alias])) {
			foreach ($results as $i => $result) {
				$customFields = array();
				if (isset($result['Meta']) && count($result['Meta']) > 0) {
					$customFields = Hash::combine($result, 'Meta.{n}.key', 'Meta.{n}.value');
				}
				$results[$i]['CustomFields'] = $customFields;
			}
		} elseif (isset($results[$model->alias])) {
			$customFields = array();
			if (isset($results['Meta']) && count($results['Meta']) > 0) {
				$customFields = Hash::combine($results, 'Meta.{n}.key', 'Meta.{n}.value');
			}
			$results['CustomFields'] = $customFields;
		}

		return $results;
	}

/**
 * Prepare data
 *
 * @param Model $model
 * @param array $data
 * @return array
 */
	public function prepareData(Model $model, $data) {
		return $this->_prepareMeta($data);
	}

/**
 * Protected method for MetaBehavior::prepareData()
 *
 * @param Model $model
 * @param array $data
 * @return array
 */
	protected function _prepareMeta($data) {
		if (isset($data['Meta']) &&
			is_array($data['Meta']) &&
			count($data['Meta']) > 0 &&
			!Hash::numeric(array_keys($data['Meta']))) {
			$meta = $data['Meta'];
			$data['Meta'] = array();
			$i = 0;
			foreach ($meta as $metaArray) {
				$data['Meta'][$i] = $metaArray;
				$i++;
			}
		}

		return $data;
	}

/**
 * Handle Model.Node.beforeSaveNode event
 *
 * @param CakeEvent $event
 */
	public function onBeforeSaveNode($event) {
		$event->data['data'] = $this->_prepareMeta($event->data['data']);
		return true;
	}

/**
 * Save with meta
 *
 * @param Model $model
 * @param array $data
 * @param array $options
 * @return void
 * @deprecated Use standard Model::saveAll()
 */
	public function saveWithMeta(Model $model, $data, $options = array()) {
		$data = $this->_prepareMeta($data);
		return $model->saveAll($data, $options);
	}

}
