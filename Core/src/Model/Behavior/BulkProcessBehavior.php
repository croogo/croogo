<?php

namespace Croogo\Core\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Croogo\Core\Status;
use InvalidArgumentException;

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
class BulkProcessBehavior extends Behavior {

	protected $_defaultConfig = [
		'fields' => [
			'status' => 'status',
			'promote' => 'promote',
		],
		'actionsMap' => [
			'delete' => 'bulkDelete',
			'publish' => 'bulkPublish',
			'promote' => false,
			'unpublish' => 'bulkUnpublish',
			'unpromote' => false,
			'copy' => 'bulkCopy',
		],
	];

/**
 * Bulk process using $action for each $ids
 *
 * @param Table $table Table object
 * @param $action string actionToPerfom
 * @param $ids array nodes ids to perform action upon
 * @return bool True when successful, false otherwise
 * @throws InvalidArgumentException
 */
	public function processAction($action, $ids) {
		$table = $this->_table;

		$actionsMap = $this->config('actionsMap');

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

		if (in_array($mappedAction, get_class_methods($table))) {
			return $table->{$mappedAction}($ids);
		}

		return $this->{$mappedAction}($table, $ids);
	}

/**
 * Internal helper method to save status fields
 *
 * @param Table $table Model object
 * @param array $ids Array of IDs
 * @param string $field Field to update
 * @param mixed $status Value to update
 * @return boolean True on success, false on failure
 */
	protected function _saveStatus(Table $table, $ids, $field, $status) {
		foreach ($ids as $id) {
			$entity = $table->get($id);
			$entity->{$field} = $status;
			if (!$table->save($entity)) {
				return false;
			}
		}

		return true;
	}

/**
 * Bulk Publish
 *
 * @param Table $table Model object
 * @param array $ids Array of IDs
 * @return boolean True on success, false on failure
 */
	public function bulkPublish(Table $table, $ids) {
		$field = $this->config('fields.status');
		return $this->_saveStatus($table, $ids, $field, Status::PUBLISHED);
	}

/**
 * Bulk Publish
 *
 * @param Table $table Model object
 * @param array $ids Array of IDs
 * @return boolean True on success, false on failure
 */
	public function bulkUnpublish(Table $table, $ids) {
		$field = $this->config('fields.status');
		return $this->_saveStatus($table, $ids, $field, Status::UNPUBLISHED);
	}

/**
 * Bulk Promote
 *
 * @param Table $table Model object
 * @param array $ids Array of IDs
 * @return boolean True on success, false on failure
 */
	public function bulkPromote(Table $table, $ids) {
		$field = $this->config('fields.promote');
		return $this->_saveStatus($table, $ids, $field, Status::PROMOTED);
	}

/**
 * Bulk Unpromote
 *
 * @param Table $table Model object
 * @param array $ids Array of IDs
 * @return boolean True on success, false on failure
 */
	public function bulkUnpromote(Table $table, $ids) {
		$field = $this->config('fields.promote');
		return $this->_saveStatus($table, $ids, $field, Status::UNPROMOTED);
	}

/**
 * Bulk Delete
 *
 * @param Table $table Model object
 * @param array $ids Array of IDs
 * @return boolean True on success, false on failure
 */
	public function bulkDelete(Table $table, $ids) {
		return $table->deleteAll([
			'id IN' => $ids
		]);
	}

/**
 * Bulk Copy
 *
 * @param Table $model Model object
 * @param array $ids Array of IDs
 * @return boolean True on success, false on failure
 */
	public function bulkCopy(Table $table, $ids) {
		if (!$table->hasBehavior('Copyable')) {
			$table->addBehavior('Croogo/Core.Copyable');
		}

		foreach ($ids as $id) {
			if (!$table->copy($id)) {
				return false;
			}
		}

		return true;
	}

}
