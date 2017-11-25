<?php

namespace Croogo\Core\Model\Behavior;

use Cake\Database\Exception;
use Cake\Log\Log;
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
class BulkProcessBehavior extends Behavior
{

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
 * @param $action string actionToPerfom
 * @param $ids array nodes ids to perform action upon
 * @return bool True when successful, false otherwise
 * @throws InvalidArgumentException
 */
    public function processAction($action, $ids)
    {
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

        return $this->{$mappedAction}($ids);
    }

/**
 * Internal helper method to save status fields
 *
 * @param array $ids Array of IDs
 * @param string $field Field to update
 * @param mixed $status Value to update
 * @return boolean True on success, false on failure
 */
    protected function _saveStatus($ids, $field, $status)
    {
        foreach ($ids as $id) {
            $entity = $this->_table->get($id);
            $entity->{$field} = $status;
            if (!$this->_table->save($entity)) {
                return false;
            }
        }

        return true;
    }

/**
 * Bulk Publish
 *
 * @param array $ids Array of IDs
 * @return boolean True on success, false on failure
 */
    public function bulkPublish($ids)
    {
        $field = $this->config('fields.status');
        return $this->_saveStatus($ids, $field, Status::PUBLISHED);
    }

/**
 * Bulk Publish
 *
 * @param array $ids Array of IDs
 * @return boolean True on success, false on failure
 */
    public function bulkUnpublish($ids)
    {
        $field = $this->config('fields.status');
        return $this->_saveStatus($ids, $field, Status::UNPUBLISHED);
    }

/**
 * Bulk Promote
 *
 * @param array $ids Array of IDs
 * @return boolean True on success, false on failure
 */
    public function bulkPromote($ids)
    {
        $field = $this->config('fields.promote');
        return $this->_saveStatus($ids, $field, Status::PROMOTED);
    }

/**
 * Bulk Unpromote
 *
 * @param array $ids Array of IDs
 * @return boolean True on success, false on failure
 */
    public function bulkUnpromote($ids)
    {
        $field = $this->config('fields.promote');
        return $this->_saveStatus($ids, $field, Status::UNPROMOTED);
    }

/**
 * Bulk Delete
 *
 * @param array $ids Array of IDs
 * @return boolean True on success, false on failure
 */
    public function bulkDelete($ids)
    {
        try {
            return $this->_table->connection()->transactional(function () use ($ids) {
                $nodes = $this->_table
                    ->find()
                    ->where([
                        $this->_table->aliasField('id') . ' IN' => $ids
                    ])
                    ->toArray();
                foreach ($nodes as $node) {
                    if (!$this->_table->delete($node)) {
                        return false;
                    }
                }

                return true;
            });
        } catch (\Exception $exception) {
            Log::critical(__FUNCTION__ . ': ' . $exception->getMessage());
            return false;
        }
    }

/**
 * Bulk Copy
 *
 * @param array $ids Array of IDs
 * @return boolean True on success, false on failure
 */
    public function bulkCopy($ids)
    {
        if (!$this->_table->hasBehavior('Copyable')) {
            $this->_table->addBehavior('Croogo/Core.Copyable');
        }

        foreach ($ids as $id) {
            if (!$this->_table->copy($id)) {
                return false;
            }
        }

        return true;
    }
}
