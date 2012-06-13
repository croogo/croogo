<?php
/**
 * UserAco Behavior
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
class UserAcoBehavior extends ModelBehavior {

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
		if (!$created) {
			$parent = $model->parentNode();
			$parent = $model->node($parent);
			$node = $model->node();
			$aro = $node[0];
			$aro['Aro']['parent_id'] = $parent[0]['Aro']['id'];
			$model->Aro->save($aro);
		}
	}

}
