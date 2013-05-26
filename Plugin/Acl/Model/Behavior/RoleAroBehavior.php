<?php

App::uses('ModelBehavior', 'Model');

/**
 * RoleAro Behavior
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
class RoleAroBehavior extends ModelBehavior {

/**
 * parentNode
 *
 * @param Model $model
 * @return $mixed
 */
	public function parentNode($model) {
		if (!$model->id && empty($model->data)) {
			return null;
		} else {
			$id = $model->id ? $model->id : $model->data[$model->alias]['id'];
			$aro = $model->Aro->node('first', array(
				'conditions' => array(
					'model' => $model->alias,
					'foreign_key' => $id,
					)
				));
			if (!empty($aro['Aro']['foreign_key'])) {
				$return = array(
					$aro[0]['Aro']['model'] => array(
						'id' => $aro['Aro']['foreign_key']
					));
			} else {
				$return = null;
			}
			return $return;
		}
	}

/**
 * afterSave
 *
 * Update the corresponding ACO record alias
 */
	public function afterSave(Model $model, $created) {
		$node = $model->node();
		$aro = $node[0];
		if (!empty($model->data[$model->alias]['alias'])) {
			$aro['Aro']['alias'] = sprintf('Role-%s',
				Inflector::slug($model->data[$model->alias]['alias'])
			);
		}
		if (!empty($model->data[$model->alias]['parent_id'])) {
			$aro['Aro']['parent_id'] = $model->data['Role']['parent_id'];
		}
		$model->Aro->save($aro);
		Cache::clearGroup('acl', 'permissions');
	}

/**
 * bindAro
 *
 * binds Aro model so that it gets retrieved during admin_[edit|add].
 */
	public function bindAro(Model $model) {
		$model->bindModel(array(
			'hasOne' => array(
				'Aro' => array(
					'foreignKey' => false,
					'conditions' => array(
						sprintf("model = '%s'", $model->alias),
						sprintf('foreign_key = %s.%s', $model->alias, $model->primaryKey),
					),
				),
			),
		), false);
	}

/**
 * afterFind
 *
 * When 'parent_id' is present, copy its value from Aro to Role data.
 */
	public function afterFind(Model $model, $results, $primary) {
		if (!empty($results[0]['Aro']['parent_id'])) {
			$results[0][$model->alias]['parent_id'] = $results[0]['Aro']['parent_id'];
			return $results;
		}
	}

/**
 * Retrieve a list of allowed parent roles
 *
 * @paraam integer $roleId
 * @param integer $id Role id
 * @return array list of allowable parent roles in 'list' format
 */
	public function allowedParents(Model $model, $id = null) {
		if (!$model->Behaviors->enabled('Croogo.Aliasable')) {
			$model->Behaviors->load('Croogo.Aliasable');
		}
		if ($id == $model->byAlias('public')) {
			return array();
		}
		$adminRoleId = $model->byAlias('admin');
		$excludes = Hash::filter(array_values(array($adminRoleId, $id)));
		$options = array('conditions' => array(
			'NOT' => array($model->alias . '.id' => $excludes),
		));
		return $model->find('list', $options);
	}

/**
 * afterDelete
 */
	public function afterDelete(Model $model) {
		Cache::clearGroup('acl', 'permissions');
	}

}
