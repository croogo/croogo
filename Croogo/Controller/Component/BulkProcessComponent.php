<?php

namespace Croogo\Croogo\Controller\Component;

use Cake\Controller\Component;
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
class BulkProcessComponent extends Component {

/**
 * Other components used by this component
 *
 * @var array
 * @access public
 */
	public $components = array(
		'Session',
	);

/**
 * controller
 *
 * @var Controller
 */
	protected $_controller = null;

/**
 * Startup
 */
	public function startup(Controller $controller) {
		parent::startup($controller);
		$this->_controller = $controller;
	}

/**
 * Get variables used for bulk processing
 *
 * @param string $model Model alias
 * @param string $primaryKey Primary key
 * @return array Array with 2 elements. First element is action name, second is
 *               array of model IDs
 */
	public function getRequestVars($model, $primaryKey = 'id') {
		$data = $this->_controller->request->data($model);
		$action = !empty($data['action']) ? $data['action'] : null;
		$ids = array();
		foreach ($data as $id => $value) {
			if (is_array($value) && !empty($value[$primaryKey])) {
				$ids[] = $id;
			}
		}
		return array($action, $ids);
	}

/**
 * Convenience method to check for selection count and redirect request
 *
 * @param bool $condition True will redirect request to $options['redirect']
 * @param array $options Options array as passed to process()
 * @return bool True if selection is valid
 */
	protected function _validateSelection($condition, $options, $messageName) {
		$messageMap = $options['messageMap'];
		$message = $messageMap[$messageName];

		if ($condition === true) {
			$this->Session->setFlash($message, 'default', array('class' => 'error'));
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
 * @param Model $Model Model instance
 * @param string $action Action name to process
 * @param array $ids Array of IDs
 * @param array $options Options
 * @return void
 */
	public function process(Model $Model, $action, $ids, $options = array()) {
		$Controller = $this->_controller;
		$emptyMessage = __d('croogo', 'No item selected');
		$noMultipleMessage = __d('croogo', 'Please choose only one item for this operation');
		$options = Hash::merge(array(
			'multiple' => array(),
			'redirect' => array(
				'action' => 'index',
			),
			'messageMap' => array(
				'empty' => $emptyMessage,
				'noMultiple' => $noMultipleMessage,
			),
		), $options);
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

		if (!$Model->Behaviors->loaded('BulkProcess')) {
			$Model->Behaviors->load('Croogo.BulkProcess');
		}

		$processed = $Model->processAction($action, $ids);
		$eventName = 'Controller.' . $Controller->name . '.after' . ucfirst($action);

		if ($processed) {
			if (!empty($messageMap[$action])) {
				$message = $messageMap[$action];
			} else {
				$message = __d('croogo', '%s processed', Inflector::humanize($Model->alias));
			}
			$setFlashOptions = array('class' => 'success');
			Croogo::dispatchEvent($eventName, $Controller, compact($ids));
		} else {
			$message = __d('croogo', 'An error occured');
			$setFlashOptions = array('class' => 'error');
		}
		$this->Session->setFlash($message, 'default', $setFlashOptions);

		return $Controller->redirect($options['redirect']);
	}

}
