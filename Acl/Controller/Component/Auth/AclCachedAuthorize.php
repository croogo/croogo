<?php

namespace Croogo\Acl\Controller\Component\Auth;

use App\Controller\Component\Auth\BaseAuthorize;
/**
 * An authentication adapter for AuthComponent. Provides similar functionality
 * to ActionsAuthorize class from CakePHP core _with_ caching capability.
 *
 * @package  Croogo.Acl.Controller.Component.Auth
 * @since    1.5
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @see      RowLevelAclComponent
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclCachedAuthorize extends BaseAuthorize {

/**
 * Constructor
 */
	public function __construct(ComponentRegistry $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		$this->_setPrefixMappings();
	}

/**
 * sets the crud mappings for prefix routes.
 *
 * @return void
 */
	protected function _setPrefixMappings() {
		if (!Configure::read('Access Control.rowLevel')) {
			return;
		}

		$crud = array('create', 'read', 'update', 'delete');
		$map = array_combine($crud, $crud);

		$Controller = $this->Controller();
		if ($Controller->Components->attached('RowLevelAcl')) {
			$settings = $Controller->Components->RowLevelAcl->settings;
			if (isset($settings['actionMap'])) {
				$map = array_merge($map, $settings['actionMap']);
			}
		}

		$prefixes = Router::prefixes();
		if (!empty($prefixes)) {
			foreach ($prefixes as $prefix) {
				$map = array_merge($map, array(
					$prefix . '_moveup' => 'update',
					$prefix . '_movedown' => 'update',
					$prefix . '_process' => 'delete',
					$prefix . '_index' => 'read',
					$prefix . '_add' => 'create',
					$prefix . '_edit' => 'update',
					$prefix . '_view' => 'read',
					$prefix . '_remove' => 'delete',
					$prefix . '_create' => 'create',
					$prefix . '_read' => 'read',
					$prefix . '_update' => 'update',
					$prefix . '_delete' => 'delete'
				));
			}
		}
		$this->mapActions($map);
	}

/**
 * Checks whether $user is an administrator
 *
 * @param bool True if user has administrative role
 */
	protected function _isAdmin($user) {
		static $Role = null;
		if (empty($user['role_id'])) {
			return false;
		}
		if (empty($this->_adminRole)) {
			if (empty($Role)) {
				$Role = ClassRegistry::init('Users.Role');
				$Role->Behaviors->attach('Croogo.Aliasable');
			}
			$this->_adminRole = $Role->byAlias('admin');
		}
		return $user['role_id'] == $this->_adminRole;
	}

/**
 * Get the action path for a given request.
 *
 * @see BaseAuthorize::action()
 */
	public function action(Request $request, $path = '/:plugin/:controller/:action') {
		$apiPath = Configure::read('Croogo.Api.path');
		if (!$request->is('api')) {
			$path = str_replace(
				array($apiPath, ':prefix/'),
				array(null, null),
				$path);
			return parent::action($request, $path);
		}

		$api = isset($request['api']) ? $apiPath : null;
		if (isset($request['prefix'])) {
			$prefix = $request['prefix'];
			$action = str_replace($request['prefix'] . '_', '', $request['action']);
		} else {
			$prefix = null;
			$action = $request['action'];
		}
		$plugin = empty($request['plugin']) ? null : Inflector::camelize($request['plugin']);
		$controller = Inflector::camelize($request['controller']);

		$path = str_replace(
			array($apiPath, ':prefix', ':plugin', ':controller', ':action'),
			array($api, $prefix, $plugin, $controller, $action),
			$this->settings['actionPath'] . $path
		);
		$path = str_replace('//', '/', $path);
		return trim($path, '/');
	}

/**
 * check request request authorization
 *
 */
	public function authorize($user, Request $request) {
		// Admin role is allowed to perform all actions, bypassing ACL
		if ($this->_isAdmin($user)) {
			return true;
		}

		$allowed = false;
		$Acl = $this->_registry->load('Acl');
		list($plugin, $userModel) = pluginSplit($this->settings['userModel']);

		$path = '/:plugin/:controller/:action';
		if ($request->is('api')) {
			$path = '/:prefix' . $path;
		}
		$action = $this->action($request, $path);

		$cacheName = 'permissions_' . strval($user['id']);
		if (($permissions = Cache::read($cacheName, 'permissions')) === false) {
			$permissions = array();
			Cache::write($cacheName, $permissions, 'permissions');
		}

		if (!isset($permissions[$action])) {
			$User = ClassRegistry::init($this->settings['userModel']);
			$User->id = $user['id'];
			$allowed = $Acl->check($User, $action);
			$permissions[$action] = $allowed;
			Cache::write($cacheName, $permissions, 'permissions');
			$hit = false;
		} else {
			$allowed = $permissions[$action];
			$hit = true;
		}

		if (Configure::read('debug')) {
			$status = $allowed ? ' allowed.' : ' denied.';
			$cached = $hit ? ' (cache hit)' : ' (cache miss)';
			Log::write(LOG_ERR, $user['username'] . ' - ' . $action . $status . $cached);
		}

		if (!$allowed) {
			return false;
		}

		if (!Configure::read('Access Control.rowLevel')) {
			return $allowed;
		}

		// bail out when controller's primary model does not want row level acl
		$controller = $this->controller();
		$model = $controller->modelClass;
		$Model = $controller->{$model};
		if ($Model && !$Model->Behaviors->attached('RowLevelAcl')) {
			return $allowed;
		}

		$primaryKey = $Model->primaryKey;
		$ids = array();
		if ($request->is('get') && !empty($request->params['pass'][0])) {
			// collect id from actions such as: Nodes/admin_edit/1
			$ids[] = $request->params['pass'][0];
		} elseif (($request->is('post') || $request->is('put')) && isset($request->data[$model]['action'])) {
			// collect ids from 'bulk' processing action such as: Nodes/admin_process
			foreach ($request->data[$model] as $id => $flag) {
				if (isset($flag[$primaryKey]) && $flag[$primaryKey] == 1) {
					$ids[] = $id;
				}
			}
		}
		foreach ($ids as $id) {
			if (is_numeric($id)) {
				try {
					$allowed = $this->_authorizeByContent($user, $request, $id);
				} catch (CakeException $e) {
					$allowed = false;
				}
			} else {
				continue;
			}
			if (!$allowed) {
				break;
			}
		}

		return $allowed;
	}

/**
 * Checks authorization by content
 *
 * @throws CakeException
 */
	protected function _authorizeByContent($user, Request $request, $id) {
		if (!isset($this->settings['actionMap'][$request->params['action']])) {
			throw new CakeException(
				__d('croogo', '_authorizeByContent() - Access of un-mapped action "%1$s" in controller "%2$s"',
				$request->action,
				$request->controller
			));
		}

		list($plugin, $userModel) = pluginSplit($this->settings['userModel']);
		$acoNode = array(
			'model' => $this->_Controller->modelClass,
			'foreign_key' => $id,
		);
		$alias = sprintf('%s.%s', $acoNode['model'], $acoNode['foreign_key']);
		$action = $this->settings['actionMap'][$request->params['action']];

		$cacheName = 'permissions_content_' . strval($user['id']);
		if (($permissions = Cache::read($cacheName, 'permissions')) === false) {
			$permissions = array();
			Cache::write($cacheName, $permissions, 'permissions');
		}

		if (!isset($permissions[$alias][$action])) {
			$Acl = $this->_registry->load('Acl');
			try {
				$allowed = $Acl->check(array($userModel => $user), $acoNode, $action);
			} catch (Exception $e) {
				Log::warning('authorizeByContent: ' . $e->getMessage());
				$allowed = false;
			}
			$permissions[$alias][$action] = $allowed;
			Cache::write($cacheName, $permissions, 'permissions');
			$hit = false;
		} else {
			$allowed = $permissions[$alias][$action];
			$hit = true;
		}

		if (Configure::read('debug')) {
			$status = $allowed ? ' allowed.' : ' denied.';
			$cached = $hit ? ' (cache hit)' : ' (cache miss)';
			Log::write(LOG_ERR, $user['username'] . ' - ' . $action . '/' . $id . $status . $cached);
		}
		return $allowed;
	}

}
