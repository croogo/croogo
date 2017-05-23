<?php

namespace Croogo\Core\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Croogo\Core\Croogo;

/**
 * BulkProcess Component
 *
 * @category Component
 * @package  Croogo.Croogo.Controller.Component
 * @version  1.0
 * @author   Rachman Chavik
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class BulkProcessComponent extends Component
{

    public $components = [
        'Flash'
    ];

/**
 * controller
 *
 * @var Controller
 */
    protected $_controller = null;

/**
 * beforeFilter
 */
    public function beforeFilter(Event $event)
    {
        $this->_controller = $event->subject();
        if ($this->_controller->request->param('action') == 'process') {
            $this->_controller->Security->config('validatePost', false);
            $this->_controller->eventManager()->off($this->_controller->Csrf);
        }

    }

/**
 * Get variables used for bulk processing
 *
 * @param string $model Model alias
 * @param string $primaryKey Primary key
 * @return array Array with 2 elements. First element is action name, second is
 *               array of model IDs
 */
    public function getRequestVars($model, $primaryKey = 'id')
    {
        $data = $this->_controller->request->data($model);
        $action = $this->_controller->request->data('action');
        $ids = [];
        foreach ($data as $id => $value) {
            if (is_array($value) && !empty($value[$primaryKey])) {
                $ids[] = $id;
            }
        }
        return [$action, $ids];
    }

/**
 * Convenience method to check for selection count and redirect request
 *
 * @param bool $condition True will redirect request to $options['redirect']
 * @param array $options Options array as passed to process()
 * @return bool True if selection is valid
 */
    protected function _validateSelection($condition, $options, $messageName)
    {
        $messageMap = $options['messageMap'];
        $message = $messageMap[$messageName];

        if ($condition === true) {
            $this->Flash->error($message);
            $this->_controller->redirect($options['redirect']);
        }
        return !$condition;
    }

/**
 * Process Bulk Request
 *
 * Operates on $Model object and assumes that bulk processing will be delegated
 * to BulkProcessBehavior
 *
 * Options:
 * - redirect URL to redirect in array format
 * - messageMap Map of error name and its message
 *
 * @param Table $table Table instance
 * @param string $action Action name to process
 * @param array $ids Array of IDs
 * @param array $options Options
 * @return void
 */
    public function process(Table $table, $action, $ids, $options = [])
    {
        $Controller = $this->_controller;
        $emptyMessage = __d('croogo', 'No item selected');
        $noMultipleMessage = __d('croogo', 'Please choose only one item for this operation');
        $options = Hash::merge([
            'multiple' => [],
            'redirect' => [
                'action' => 'index',
            ],
            'messageMap' => [
                'empty' => $emptyMessage,
                'noMultiple' => $noMultipleMessage,
            ],
        ], $options);
        $messageMap = $options['messageMap'];
        $itemCount = count($ids);

        $noItems = $itemCount === 0 || $action == null;
        $valid = $this->_validateSelection($noItems, $options, 'empty');
        if (!$valid) {
            return;
        }

        $tooMany = isset($options['multiple'][$action]) && $options['multiple'][$action] === false && $itemCount > 1;
        $valid = $this->_validateSelection($tooMany, $options, 'noMultiple');
        if (!$valid) {
            return false;
        }

        if (!$table->hasBehavior('BulkProcess')) {
            $table->addBehavior('Croogo/Core.BulkProcess');
        }

        $processed = $table->processAction($action, $ids);
        $eventName = 'Controller.' . $Controller->name . '.after' . ucfirst($action);

        if ($processed) {
            if (!empty($messageMap[$action])) {
                $message = $messageMap[$action];
            } else {
                $message = __d('croogo', '%s processed', Inflector::humanize($table->alias()));
            }
            $flashMethod = 'success';
            Croogo::dispatchEvent($eventName, $Controller, compact($ids));
        } else {
            $message = __d('croogo', 'An error occured');
            $flashMethod = 'error';
        }
        $this->Flash->{$flashMethod}($message);

        return $Controller->redirect($options['redirect']);
    }
}
