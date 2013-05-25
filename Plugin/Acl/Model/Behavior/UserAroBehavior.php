<?php

App::uses('ModelBehavior', 'Model');

/**
 * UserAro Behavior
 *
 * PHP version 5
 *
 * @category Behavior
 * @package  Croogo.Acl.Model.Behavior
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class UserAroBehavior extends ModelBehavior {

/**
 * Setup
 */
	public function setup(Model $model, $config = array()) {
		$this->_setupMultirole($model);
	}

/**
 * Enable Multiple Role, dynamically bind User Habtm Role
 */
	protected function _setupMultirole(Model $model) {
		if (!Configure::read('Access Control.multiRole')) {
			return;
		}
		$model->bindModel(array(
			'hasAndBelongsToMany' => array(
				'Role' => array(
					'className' => 'Users.Role',
					'unique' => 'keepExisting',
				),
			)
		), false);
	}

/**
 * parentNode
 *
 * @param Model $model
 * @return array
 */
	public function parentNode($model) {
		if (!$model->id && empty($model->data)) {
			return null;
		}
		$data = $model->data;
		if (empty($model->data)) {
			$data = $model->read();
		}
		if (!isset($data['User']['role_id'])) {
			$data['User']['role_id'] = $model->field('role_id');
		}
		if (!isset($data['User']['role_id']) || !$data['User']['role_id']) {
			return null;
		} else {
			return array('Role' => array('id' => $data['User']['role_id']));
		}
	}

/**
 * afterSave
 *
 * @param Model $model
 * @param boolean $created
 * @return void
 */
	public function afterSave(Model $model, $created) {
		// update ACO alias
		if (!empty($model->data['User']['username'])) {
			$node = $model->node();
			$aro = $node[0];
			$model->Aro->id = $aro['Aro']['id'];
			$model->Aro->saveField('alias', $model->data['User']['username']);
		}
		Cache::clearGroup('acl', 'permissions');
	}

/**
 * afterDelete
 */
	public function afterDelete(Model $model) {
		Cache::clearGroup('acl', 'permissions');
	}

}
