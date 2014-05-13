<?php

App::uses('Component', 'Controller');

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
			if (!empty($value[$primaryKey])) {
				$ids[] = $id;
			}
		}
		return array($action, $ids);
	}

/**
 * Process
 */
	public function process(Model $Model, $action, $ids, $options = array()) {
		$Controller = $this->_controller;
		$emptyMessage = __d('croogo', 'No item selected');
		$options = Hash::merge(array(
			'redirect' => array(
				'action' => 'index',
			),
			'messageMap' => array(
				'empty' => $emptyMessage,
			),
		), $options);
		$messageMap = $options['messageMap'];

		if ((count($ids) === 0 || $action == null)) {
			if (!empty($messageMap['empty'])) {
				$message = $messageMap['empty'];
			} else {
				$message = $emptyMessage;
			}
			$this->Session->setFlash($message, 'default', array('class' => 'error'));
			return $Controller->redirect(array('action' => 'index'));
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
