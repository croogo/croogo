<?php

/**
 * RowLevelAcl Behavior
 *
 * @package Croogo.Acl.Model.Behavior
 * @since 1.5
 */
class RowLevelAclBehavior extends ModelBehavior {

/**
 * parentNode
 *
 * @param $model Model model instance
 */
	public function parentNode($model) {
		if (!$model->id && empty($model->data)) {
			return null;
		} else {
			$alias = $model->alias;
			if ($model->id) {
				$id = $model->id;
			} else {
				$id = $model->data[$alias][$model->primaryKey];
			}
			$aco = $model->Aco->find('first', array(
				'conditions' => array(
					'model' => $alias,
					'foreign_key' => $id,
					)
				));
			if (empty($aco['Aco']['foreign_key'])) {
				$return = 'contents';
			} else {
				$return = array($alias => array(
					'id' => $aco['Aco']['foreign_key']
					));
			}
			return $return;
		}
	}

/**
 * afterSave
 *
 * Creates an ACO record for current model record, and give permissions to the
 * creator. If 'RolePermission' is present, 'grant' or 'inherit' permissions
 * for the role.
 */
	public function afterSave(Model $model, $created) {
		if (empty($model->data[$model->alias][$model->primaryKey])) {
			return;
		}
		$node = $model->node();
		$aco = $node[0];
		$alias = $model->alias;
		$aco['Aco']['alias'] = sprintf(
			'%s.%s', $alias, $model->data[$alias][$model->primaryKey]
			);
		$model->Aco->save($aco);

		if ($created && $user = AuthComponent::user()) {
			$aro = array('User' => $user);
			$model->Aco->Permission->allow($aro, $aco['Aco']['alias']);
		}

		if (!empty($model->data['RolePermission'])) {
			foreach ($model->data['RolePermission'] as $roleId => $checked) {
				$aro = array('model' => 'Role', 'foreign_key' => $roleId);
				$aco = array('model' => $model->alias, 'foreign_key' => $model->id);
				$model->Aco->Permission->allow($aro, $aco, '*', $checked ? 1 : 0);
			}
		}
	}

}
