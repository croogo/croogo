<?php

namespace Croogo\Croogo\Model\Behavior;

use App\Model\ModelBehavior;
use Croogo\Lib\CroogoStatus;
/**
 * BulkProcess Behavior
 *
 * Utility Behavior to allow easy bulk processing.
 *
 * Behavior options:
 * - fields:
 *   map of field and its physical names
 * - actions:
 *   map of action and its method. By default, delete, publish, and
 *   unpublish are supported. You can add or override the default methods
 *   by implementing it in the model. These methods needs to accept one
 *   argument, containing an array of IDs.
 *
 * @package Croogo.Croogo.Model.Behavior
 * @since 2.0
 * @author Rachman Chavik <rchavik@xintesa.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link http://www.croogo.org
 *
 */
class BulkProcessBehavior extends ModelBehavior {

/**
 * Setup
 */
	public function setup(Model $model, $config = array()) {
		$defaults = array(
			'fields' => array(
				'status' => 'status',
				'promote' => 'promote',
			),
			'actionsMap' => array(
				'delete' => 'bulkDelete',
				'publish' => 'bulkPublish',
				'promote' => false,
				'unpublish' => 'bulkUnpublish',
				'unpromote' => false,
				'copy' => 'bulkCopy',
			),
		);
		$config = Hash::merge($defaults, $config);
		$this->settings[$model->alias] = $config;
	}

/**
 * Bulk process using $action for each $ids
 *
 * @param Model $model Model object
 * @param $action string actionToPerfom
 * @param $ids array nodes ids to perform action upon
 * @return bool True when successful, false otherwise
 * @throws InvalidArgumentException
 */
	public function processAction(Model $model, $action, $ids) {
		$settings = $this->settings[$model->alias];
		$actionsMap = $settings['actionsMap'];

		if (empty($actionsMap[$action])) {
			throw new InvalidArgumentException(__d('croogo', 'Invalid action to perform'));
		}

		if (empty($ids)) {
			throw new InvalidArgumentException(__d('croogo', 'No target to process action upon'));
		}

		$mappedAction = $actionsMap[$action];

		if ($mappedAction === false) {
			throw new InvalidArgumentException(__d('croogo', 'Action %s is disabled'), $action);
		}

		if (in_array($mappedAction, get_class_methods($model))) {
			return $model->{$mappedAction}($ids);
		}

		return $this->{$mappedAction}($model, $ids);
	}

/**
 * Internal helper method to save status fields
 *
 * @param Model $model Model object
 * @param array $ids Array of IDs
 * @param string $field Field to update
 * @param mixed $status Value to update
 * @return boolean True on success, false on failure
 */
	protected function _saveStatus(Model $model, $ids, $field, $status) {
		return $model->updateAll(
			array($model->escapeField($field) => $status),
			array($model->escapeField() => $ids)
		);
	}

/**
 * Bulk Publish
 *
 * @param Model $model Model object
 * @param array $ids Array of IDs
 * @return boolean True on success, false on failure
 */
	public function bulkPublish(Model $model, $ids) {
		$field = $this->settings[$model->alias]['fields']['status'];
		return $this->_saveStatus($model, $ids, $field, CroogoStatus::PUBLISHED);
	}

/**
 * Bulk Publish
 *
 * @param Model $model Model object
 * @param array $ids Array of IDs
 * @return boolean True on success, false on failure
 */
	public function bulkUnpublish(Model $model, $ids) {
		$field = $this->settings[$model->alias]['fields']['status'];
		return $this->_saveStatus($model, $ids, $field, CroogoStatus::UNPUBLISHED);
	}

/**
 * Bulk Promote
 *
 * @param Model $model Model object
 * @param array $ids Array of IDs
 * @return boolean True on success, false on failure
 */
	public function bulkPromote(Model $model, $ids) {
		$field = $this->settings[$model->alias]['fields']['promote'];
		return $this->_saveStatus($model, $ids, $field, CroogoStatus::PROMOTED);
	}

/**
 * Bulk Unpromote
 *
 * @param Model $model Model object
 * @param array $ids Array of IDs
 * @return boolean True on success, false on failure
 */
	public function bulkUnpromote(Model $model, $ids) {
		$field = $this->settings[$model->alias]['fields']['promote'];
		return $this->_saveStatus($model, $ids, $field, CroogoStatus::UNPROMOTED);
	}

/**
 * Bulk Delete
 *
 * @param Model $model Model object
 * @param array $ids Array of IDs
 * @return boolean True on success, false on failure
 */
	public function bulkDelete(Model $model, $ids) {
		return $model->deleteAll(array($model->escapeField() => $ids), true, true);
	}

/**
 * Bulk Copy
 *
 * @param Model $model Model object
 * @param array $ids Array of IDs
 * @return boolean True on success, false on failure
 */
	public function bulkCopy(Model $model, $ids) {
		if (!$model->Behaviors->loaded('Copyable')) {
			$model->Behaviors->load('Croogo.Copyable');
		}
		$result = false;
		$ds = $model->getDataSource();
		$ds->begin();
		foreach ($ids as $id) {
			$result = $model->copy($id);
			if (!$result) {
				$ds->rollback();
				break;
			}
		}
		if ($result) {
			$ds->commit();
		}
		return $result;
	}

}
