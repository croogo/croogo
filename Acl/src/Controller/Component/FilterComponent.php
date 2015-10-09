<?php

namespace Croogo\Acl\Controller\Component;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Controller\Component;
use Cake\Controller\Component\AuthComponent;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Croogo\Core\Croogo;

/**
 * AclFilter Component
 *
 * @category Component
 * @package  Croogo.Acl.Controller.Component
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class FilterComponent extends Component {

/**
 * _controller
 *
 * @var Controller
 */
	protected $_controller = null;

/**
 * beforeFilter
 *
 * @param Event $event instance of event
 * @return void
 */
	public function beforeFilter(Event $event) {
		$this->_controller = $event->subject();

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
		if (!$this->_registry->loaded('Acl.AclAutoLogin')) {
			$this->_registry->load('Acl.AclAutoLogin');
		}
		if (!$this->_registry->loaded('Cookie')) {
			$this->_registry->load('Cookie');
		}
		//Configure AuthComponent
		$this->_controller->Auth->authenticate = array(
			AuthComponent::ALL => array(
				'userModel' => 'Croogo/Users.Users',
				'fields' => array(
					'username' => 'username',
					'password' => 'password',
				),
				'passwordHasher' => [
					'className' => 'Fallback',
					'hashers' => ['Default', 'Weak']
				],
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
					$this->_controller->Session->setFlash($notice, 'flash', array('class' => 'error'));
				}
				if (isset($this->_controller->Settings)) {
					$Setting = $this->_controller->Settings;
				} else {
					$Setting = TableRegistry::get('Croogo/Settings.Settings');
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

		$actionPath = $this->_controller->request->is('api') ? 'api' : 'controllers';
		$this->_controller->Auth->authorize = array(
			AuthComponent::ALL => array(
				'actionPath' => 'controllers',
				'userModel' => 'Users.User',
			),
			'Acl.AclCached' => array(
				'actionPath' => $actionPath,
			),
		);

		if (isset($this->_controller->request->params['admin']) &&
			!$this->_controller->Auth->loggedIn()) {
			$this->_controller->Auth->authError = false;
		}

		$this->configureLoginActions();
	}

/**
 * Load login actions configurations
 *
 * @return void
 */
	public function configureLoginActions() {
		$this->_controller->Auth->config('loginAction', [
			'prefix' => false,
			'plugin' => 'Croogo/Users',
			'controller' => 'Users',
			'action' => 'login',
		]);
		if ($this->request->param('prefix') === 'admin') {
			$this->_controller->Auth->config('loginAction', [
				'prefix' => 'admin',
				'plugin' => 'Croogo/Users',
				'controller' => 'Users',
				'action' => 'login',
			]);
		}
		$this->_controller->Auth->config('logoutRedirect', [
			'plugin' => 'Croogo/Users',
			'controller' => 'Users',
			'action' => 'login',
		]);
		if ($this->_controller->request->param('admin')) {
			$this->_controller->Auth->config('loginRedirect', Configure::read('Croogo.dashboardUrl'));
		} else {
			$this->_controller->Auth->config('loginRedirect', Configure::read('Croogo.homeUrl'));
		}
		$this->_controller->Auth->config('unauthorizedRedirect', [
			'plugin' => 'Croogo/Users',
			'controller' => 'Users',
			'action' => 'login',
		]);

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

		// authorization for authenticated user is handled by authorize object
		if ($user) {
			return;
		}

		// public access authorization
		$cacheName = 'permissions_public';
		if (($perms = Cache::read($cacheName, 'permissions')) === false) {
			$perms = $this->getPermissions('Croogo/Users.Roles', 3);
			Cache::write($cacheName, $perms, 'permissions');
		}
		$actionPath = $this->_controller->request->is('api') ? 'api' : 'controllers';
		if (!empty($perms['allowed'][$actionPath][$this->_controller->name])) {
			$this->_controller->Auth->allow(
				$perms['allowed'][$actionPath][$this->_controller->name]
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
		$aro = ['model' => $model, 'foreign_key' => $id];
		$node = $Acl->Aro->node($aro)->all();
		$nodes = $Acl->Aro->find('path', ['for' => $id])->all();

		$aros = collection($node)->extract('id')->toArray();
		if (!empty($nodes)) {
			$aros = Hash::merge($aros, collection($nodes)->extract('id')->toArray());
		}

		$permissions = TableRegistry::get('Acl.Permissions')->find('all', array(
			'conditions' => array(
				'Permissions.aro_id' => $aros,
				'Permissions._create' => 1,
				'Permissions._read' => 1,
				'Permissions._update' => 1,
				'Permissions._delete' => 1,
			)
		));

		$authorized = $allowedActions = array();
		foreach ($permissions as $permission) {
			$path = $Acl->Aco->getPath($permission['Permission']['aco_id']);
			if (empty($path)) {
				continue;
			}
			$acos = count($path);
			if ($acos == 5) {
				// api controller/action
				$controller = $path[3]['Aco']['alias'];
				$action = $path[1]['Aco']['alias'] . '_' . $path[4]['Aco']['alias'];
			} elseif ($acos == 4) {
				// plugin controller/action
				$controller = $path[2]['Aco']['alias'];
				$action = $path[3]['Aco']['alias'];
			} elseif ($acos == 3) {
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
			$actionPath = $path[0]['Aco']['alias'];
			$allowedActions[$actionPath][$controller][] = $action;
			$authorized[] = implode('/', Hash::extract($path, '{n}.Aco.alias'));
		}
		return array('authorized' => $authorized, 'allowed' => $allowedActions);
	}

}
