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

	protected $controller = null;

/**
 * @param object $controller controller
 * @param array  $settings   settings
 */
	public function initialize(Controller $controller) {
		$this->controller =& $controller;
	}

/**
 * acl and auth
 *
 * @return void
 */
	public function auth() {
		//Configure AuthComponent
		$this->controller->Auth->authenticate = array(
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
		$this->controller->Auth->authorize = array(
			AuthComponent::ALL => array('actionPath' => $actionPath),
			'Actions',
			);
		$this->controller->Auth->loginAction = array(
			'plugin' => null,
			'controller' => 'users',
			'action' => 'login',
		);
		$this->controller->Auth->logoutRedirect = array(
			'plugin' => null,
			'controller' => 'users',
			'action' => 'login',
		);
		$this->controller->Auth->loginRedirect = array(
			'plugin' => null,
			'controller' => 'users',
			'action' => 'index',
		);

		if ($this->controller->Auth->user() && $this->controller->Auth->user('role_id') == 1) {
			// Role: Admin
			$this->controller->Auth->allowedActions = array('*');
		} else {
			if ($this->controller->Auth->user()) {
				$roleId = $this->controller->Auth->user('role_id');
			} else {
				$roleId = 3; // Role: Public
			}

			$allowedActions = ClassRegistry::init('Acl.AclPermission')->getAllowedActionsByRoleId($roleId);
			$linkAction = Inflector::camelize($this->params['controller']) . '/' . $this->params['action'];
			if (isset($url['admin']) && $url['admin']) {
				$linkAction = Inflector::camelize($url['controller']) . '/admin_' . $url['action'];
			}
			if (in_array($linkAction, $allowedActions)) {
				$this->controller->Auth->allowedActions = array($this->params['action']);
			}
		}
	}

}
