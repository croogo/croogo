<?php
/** Aliasable Behavior
 *
 * Utility behavior to allow easy retrieval of records by id or its alias
 *
 * @package  Croogo
 * @since  1.4
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link	 http://www.croogo.org
 */

class AliasableBehavior extends ModelBehavior {

	protected $_byIds = array();

	protected $_byAlias = array();

	public function setup(&$model, $config = array()) {
		$config = Set::merge(array(
			'id' => 'id',
			'alias' => 'alias',
			), $config);
		$this->settings[$model->alias] = $config;
		$this->reload($model);
	}

	public function reload(&$model) {
		$config = $this->settings[$model->alias];
		$this->_byIds[$model->alias] = $model->find('list', array(
			'fields' => array($config['id'], $config['alias']),
			'conditions' => array(
				$model->alias . '.' . $config['alias'] . ' != ' => '',
				),
			));
		$this->_byAlias[$model->alias] = array_flip($this->_byIds[$model->alias]);
	}

	public function byId(&$model, $id) {
		if (!empty($this->_byIds[$model->alias][$id])) {
			return $this->_byIds[$model->alias][$id];
		}
		return false;
	}

	public function byAlias(&$model, $alias) {
		if (!empty($this->_byAlias[$model->alias][$alias])) {
			return $this->_byAlias[$model->alias][$alias];
		}
		return false;
	}

	public function listById(&$model) {
		return $this->_byIds[$model->alias];
	}

	public function listByAlias(&$model) {
		return $this->_byAlias[$model->alias];
	}

}
