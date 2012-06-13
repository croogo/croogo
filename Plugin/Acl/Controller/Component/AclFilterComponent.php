<?php
/**
 * AclFilter Component
 *
 * PHP version 5
 *
 * @category Component
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclFilterComponent extends Component {

/**
 * _controller
 *
 * @var Controller
 */
	protected $_controller = null;

/**
 * @param object $controller controller
 * @param array  $settings   settings
 */
	public function initialize(Controller $controller) {
		$this->_controller =& $controller;
	}

/**
 * acl and auth
 *
 * @return void
 */
	public function auth() {
		//Configure AuthComponent
		$this->_controller->Auth->authenticate = array(
			AuthComponent::ALL => array(
				'userModel' => 'User',
				'fields' => array(
					'username' => 'username',
					'password' => 'password',
				),
				'scope' => array(
					'User.status' => 1,
				),
			),
			'Form',
		);
		$actionPath = 'controllers';
		$this->_controller->Auth->authorize = array(
			AuthComponent::ALL => array('actionPath' => $actionPath),
			'Actions',
			);
		$this->_controller->Auth->loginAction = array(
			'plugin' => null,
			'controller' => 'users',
			'action' => 'login',
		);
		$this->_controller->Auth->logoutRedirect = array(
			'plugin' => null,
			'controller' => 'users',
			'action' => 'login',
		);
		$this->_controller->Auth->loginRedirect = array(
			'plugin' => null,
			'controller' => 'users',
			'action' => 'index',
		);

		if ($this->_controller->Auth->user() && $this->_controller->Auth->user('role_id') == 1) {
			// Role: Admin
			$this->_controller->Auth->allowedActions = array('*');
		} else {
			if ($this->_controller->Auth->user()) {
				$roleId = $this->_controller->Auth->user('role_id');
			} else {
				$roleId = 3; // Role: Public
			}

			$allowedActions = ClassRegistry::init('Acl.AclPermission')->getAllowedActionsByRoleId($roleId);
			$linkAction = Inflector::camelize($this->_controller->request->params['controller']) . '/' . $this->_controller->request->params['action'];
			if (isset($this->_controller->request->params['admin']) && $this->_controller->request->params['admin']) {
				$linkAction = Inflector::camelize($this->_controller->request->params['controller']) . '/admin_' . $this->_controller->request->params['action'];
			}
			if (in_array($linkAction, $allowedActions)) {
				$this->_controller->Auth->allowedActions = array($this->_controller->request->params['action']);
			}
		}
	}

}
