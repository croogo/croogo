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
 * initialize
 *
 * @param Controller $controller instance of controller
 * @return void
 */
	public function initialize(Controller $controller) {
		$this->_controller = $controller;

		if (Configure::read('Access Control.multiRole')) {
			Croogo::hookAdminTab('Users/admin_add', 'Roles', 'Acl.admin/roles');
			Croogo::hookAdminTab('Users/admin_edit', 'Roles', 'Acl.admin/roles');
		}
	}

/**
 * configure component settings
 *
 * @return void
 */
	protected function _configure() {
		//Configure AuthComponent
		$this->_controller->Auth->authenticate = array(
			AuthComponent::ALL => array(
				'userModel' => 'Users.User',
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
		$this->_controller->Auth->authorize = array(
			AuthComponent::ALL => array(
				'actionPath' => 'controllers',
				'userModel' => 'Users.User',
			),
			'Acl.AclCached',
		);
		$this->_controller->Auth->loginAction = array(
			'plugin' => 'users',
			'controller' => 'users',
			'action' => 'login',
		);
		$this->_controller->Auth->logoutRedirect = array(
			'plugin' => 'users',
			'controller' => 'users',
			'action' => 'login',
		);
		$this->_controller->Auth->loginRedirect = array(
			'plugin' => 'users',
			'controller' => 'users',
			'action' => 'index',
		);

		$config = Configure::read('Acl');
		if (!empty($config['Auth']) && is_array($config['Auth'])) {
			foreach ($config['Auth'] as $property => $value) {
				$this->_controller->Auth->{$property} = $value;
			}
		}
	}

/**
 * acl and auth
 *
 * @return void
 */
	public function auth() {
		$this->_configure();
		$user = $this->_controller->Auth->user();
		// Admin role is allowed to perform all actions, bypassing ACL
		if (!empty($user['role_id']) && $user['role_id'] == 1) {
			$this->_controller->Auth->allow();
			return;
		}

		// authorization for authenticated user is handled by authorize object
		if ($user) {
			return;
		}

		// public access authorization
		$cacheName = 'permissions_public';
		if (($perms = Cache::read($cacheName, 'permissions')) === false) {
			$perms = $this->getPermissions('Role', 3);
			Cache::write($cacheName, $perms, 'permissions');
		}
		if (!empty($perms['allowed'][$this->_controller->name])) {
			$this->_controller->Auth->allow(
				$perms['allowed'][$this->_controller->name]
			);
		}
	}

/**
 * getPermissions
 * retrieve list of permissions from database
 * @param string $model model name
 * @param string $id model id
 * @return array list of authorized and allowed actions
 */
	public function getPermissions($model, $id) {
		$Acl =& $this->_controller->Acl;
		$aro = array('model' => $model, 'foreign_key' => $id);
		$node = $Acl->Aro->node($aro);
		$nodes = $Acl->Aro->getPath($node[0]['Aro']['id']);

		$aros = Set::extract('/Aro/id', $node);
		if (!empty($nodes)) {
			$aros = Set::merge($aros, Set::extract('/Aro/id', $nodes));
		}

		$permissions = $Acl->Aro->Permission->find('all', array(
			'conditions' => array(
				'Permission.aro_id' => $aros,
				'Permission._create' => 1,
				'Permission._read' => 1,
				'Permission._update' => 1,
				'Permission._delete' => 1,
				)
			));

		$authorized = $allowedActions = array();
		foreach ($permissions as $permission) {
			$path = $Acl->Aco->getPath($permission['Permission']['aco_id']);
			if (count($path) == 4) {
				// plugin controller/action
				$controller = $path[2]['Aco']['alias'];
				$action = $path[3]['Aco']['alias'];
			} else {
				// app controller/action
				$controller = $path[1]['Aco']['alias'];
				$action = $path[2]['Aco']['alias'];
			}
			$allowedActions[$controller][] = $action;
			$authorized[] = implode('/', Set::extract('/Aco/alias', $path));
		}
		return array('authorized' => $authorized, 'allowed' => $allowedActions);
	}

}
