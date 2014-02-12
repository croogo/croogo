<?php

App::uses('ModelBehavior', 'Model/Behavior');

/**
 * CommentableBehavior
 *
 * @category Comments.Model.Behavior
 * @package  Croogo.Comments.Model.Behavior
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CommentableBehavior extends ModelBehavior {

/**
 * Setup behavior
 *
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$this->settings[$model->alias] = $config;

		$this->_setupRelationships($model);
	}

/**
 * Setup relationships
 *
 * @return void
 */
	protected function _setupRelationships(Model $model) {
		$model->bindModel(array(
			'hasMany' => array(
				'Comment' => array(
					'className' => 'Comments.Comment',
					'foreignKey' => 'foreign_key',
					'dependent' => true,
					'limit' => 5,
					'conditions' => array(
						'model' => $model->alias,
						'status' => (bool)1,
					),
				),
			),
		), false);
	}

/**
 * beforeDelete callback
 *
 * @return boolean
 */
	public function beforeDelete(Model $model, $cascade = true) {
		if ($cascade) {
			if (isset($model->hasMany['Comment'])) {
				$model->hasMany['Comment']['conditions'] = '';
			}
		}
		return true;
	}

}
