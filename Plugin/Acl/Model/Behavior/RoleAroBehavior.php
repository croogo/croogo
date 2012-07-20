<?php
/**
 * RoleAro Behavior
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
		if (!empty($model->data['Role']['alias'])) {
			$node = $model->node();
			$aro = $node[0];
			$model->Aro->id = $aro['Aro']['id'];
			$model->Aro->saveField('alias', 'Role-' . $model->data['Role']['alias']);
		}
	}

}
