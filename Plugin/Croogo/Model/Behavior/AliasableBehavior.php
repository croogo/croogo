<?php

App::uses('ModelBehavior', 'Model');

/**
 * Aliasable Behavior
 *
 * Utility behavior to allow easy retrieval of records by id or its alias
 *
 * @package  Croogo.Croogo.Model.Behavior
 * @since    1.4
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */

class AliasableBehavior extends ModelBehavior {

/**
 * _byIds
 *
 * @var array
 */
	protected $_byIds = array();

/**
 * _byAlias
 *
 * @var array
 */
	protected $_byAlias = array();

/**
 * setup
 *
 * @param Model $model
 * @param array $config
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$config = Hash::merge(array(
			'id' => 'id',
			'alias' => 'alias',
		), $config);
		$this->settings[$model->alias] = $config;
		$this->reload($model);
	}

/**
 * reload
 *
 * @param Model $model
 * @return void
 */
	public function reload(Model $model) {
		$config = $this->settings[$model->alias];
		$this->_byIds[$model->alias] = $model->find('list', array(
			'fields' => array($config['id'], $config['alias']),
			'conditions' => array(
				$model->alias . '.' . $config['alias'] . ' != ' => '',
			),
		));
		$this->_byAlias[$model->alias] = array_flip($this->_byIds[$model->alias]);
	}

/**
 * byId
 *
 * @param Model $model
 * @param integer $id
 * @return boolean
 */
	public function byId(Model $model, $id) {
		if (!empty($this->_byIds[$model->alias][$id])) {
			return $this->_byIds[$model->alias][$id];
		}
		return false;
	}

/**
 * byAlias
 *
 * @param Model $model
 * @param string $alias
 * @return boolean
 */
	public function byAlias(Model $model, $alias) {
		if (!empty($this->_byAlias[$model->alias][$alias])) {
			return $this->_byAlias[$model->alias][$alias];
		}
		return false;
	}

/**
 * listById
 *
 * @param Model $model
 * @return string
 */
	public function listById(Model $model) {
		return $this->_byIds[$model->alias];
	}

/**
 * listByAlias
 *
 * @param Model $model
 * @return string
 */
	public function listByAlias(Model $model) {
		return $this->_byAlias[$model->alias];
	}

}
