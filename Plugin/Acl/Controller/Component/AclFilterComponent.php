<?php

App::uses('Component', 'Controller');

/**
 * AclFilter Component
 *
 * PHP version 5
 *
 * @category Component
 * @package  Croogo.Acl.Controller.Component
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

		if ($this->_config('multiRole')) {
			Croogo::hookAdminTab('Users/admin_add', 'Roles', 'Acl.admin/roles');
			Croogo::hookAdminTab('Users/admin_edit', 'Roles', 'Acl.admin/roles');
		}
	}

/**
 * Helper function to retrieve value from `Access Control` settings
 *
 * @return mixed null when config key is not found
 */
	protected function _config($key) {
		static $config = null;
		if (empty($config)) {
			$config = Configure::read('Access Control');
		}
		if (array_key_exists($key, $config)) {
			return $config[$key];
		}
		return null;
	}

/**
 * configure component settings
 *
 * @return void
 */
	protected function _configure() {
		if (!$this->_Collection->loaded('Acl.AclAutoLogin')) {
			$this->_Collection->load('Acl.AclAutoLogin');
		}
		if (!$this->_Collection->loaded('Cookie')) {
			$this->_Collection->load('Cookie');
		}
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
		);
		if ($this->_config('autoLoginDuration')) {
			if (!function_exists('mcrypt_encrypt')) {
				$notice = __d('croogo', '"AutoLogin" (Remember Me) disabled since mcrypt_encrypt is not available');
				$this->log($notice, LOG_CRIT);
				if (isset($this->_controller->request->params['admin'])) {
					$this->_controller->Session->setFlash($notice, 'default', null, array('class', 'error'));
				}
				if (isset($this->_controller->Setting)) {
					$Setting = $this->_controller->Setting;
				} else {
					$Setting = ClassRegistry::init('Settings.Setting');
				}
				$Setting->write('Access Control.autoLoginDuration', '');
			}
			$this->_controller->Auth->authenticate[] = 'Acl.Cookie';
		}
		if ($this->_config('multiColumn')) {
			$this->_controller->Auth->authenticate[] = 'Acl.MultiColumn';
		} else {
			$this->_controller->Auth->authenticate[] = 'Form';
		}

		$this->_controller->Auth->authorize = array(
			AuthComponent::ALL => array(
				'actionPath' => 'controllers',
				'userModel' => 'Users.User',
			),
			'Acl.AclCached',
		);

		$this->configureLoginActions();
	}

/**
 * Load login actions configurations
 *
 * @return void
 */
	public function configureLoginActions() {
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
		$this->_controller->Auth->unauthorizedRedirect = array(
			'plugin' => 'users',
			'controller' => 'users',
			'action' => 'login',
		);

		$config = Configure::read('Acl');
		if (!empty($config['Auth']) && is_array($config['Auth'])) {
			$isAdminRequest = !empty($this->_controller->request->params['admin']);
			$authActions = array(
				'loginAction', 'loginRedirect', 'logoutRedirect',
				'unauthorizedRedirect',
			);
			foreach ($config['Auth'] as $property => $value) {
				$isAdminRoute = !empty($value['admin']);
				$isAuthAction = in_array($property, $authActions);
				if (!is_string($value) && $isAdminRequest !== $isAdminRoute && $isAuthAction) {
					continue;
				}
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

		$aros = Hash::extract($node, '{n}.Aro.id');
		if (!empty($nodes)) {
			$aros = Hash::merge($aros, Hash::extract($nodes, '{n}.Aro.id'));
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
			if (empty($path)) {
				continue;
			}
			$acos = count($path);
			if ($acos == 4) {
				// plugin controller/action
				$controller = $path[2]['Aco']['alias'];
				$action = $path[3]['Aco']['alias'];
			} else if ($acos == 3) {
				// app controller/action
				$controller = $path[1]['Aco']['alias'];
				$action = $path[2]['Aco']['alias'];
			} else {
				$this->log(sprintf(
					'Incomplete path for aco_id = %s:',
					$permission['Permission']['id']
				));
				$this->log($path);
			}
			$allowedActions[$controller][] = $action;
			$authorized[] = implode('/', Hash::extract($path, '{n}.Aco.alias'));
		}
		return array('authorized' => $authorized, 'allowed' => $allowedActions);
	}

}
