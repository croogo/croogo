<?php
/**
 * AclCachedAuthorize
 *
 * Similar to ActionsAuthorize with cached result
 *
 * @package  Croogo.Acl
 * @since    1.5
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
App::uses('BaseAuthorize', 'Controller/Component/Auth');

class AclCachedAuthorize extends BaseAuthorize {

	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		$this->_setPrefixMappings();
	}

/**
 * sets the crud mappings for prefix routes.
 *
 * @return void
 */
	protected function _setPrefixMappings() {
		$crud = array('create', 'read', 'update', 'delete');
		$map = array_combine($crud, $crud);

		$prefixes = Router::prefixes();
		if (!empty($prefixes)) {
			foreach ($prefixes as $prefix) {
				$map = array_merge($map, array(
					$prefix . '_process' => 'update',
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
 * check request request authorization
 *
 */
	public function authorize($user, CakeRequest $request) {
		$allowed = false;
		$Acl = $this->_Collection->load('Acl');
		list($plugin, $userModel) = pluginSplit($this->settings['userModel']);
		$user = array($userModel => $user);
		$action = $this->action($request);

		$cacheName = 'permissions_' . strval($user['User']['id']);
		if (($permissions = Cache::read($cacheName, 'permissions')) === false) {
			$permissions = array();
			Cache::write($cacheName, $permissions, 'permissions');
		}

		if (!isset($permissions[$action])) {
			$User = ClassRegistry::init($this->settings['userModel']);
			$User->id = $user['User']['id'];
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
			CakeLog::write(LOG_ERR, $user['User']['username'] . ' - ' . $action . $status . $cached);
		}

		if (!$allowed) {
			return false;
		}

		$ids = array();
		if ($request->is('get') && !empty($request->params['pass'][0])) {
			$ids[] = $request->params['pass'][0];
		} elseif ($request->is('post') || $request->is('put')) {
			$model = Inflector::classify($request->params['controller']);
			foreach ($request->data[$model] as $id => $flag) {
				if (isset($flag['id']) && $flag['id'] == 1) {
					$ids[] = $id;
				}
			}
		}
		foreach ($ids as $id) {
			if (is_numeric($id)) {
				$allowed = $this->_authorizeByContent($user['User'], $request, $id);
			} else {
				continue;
			}
			if (!$allowed) {
				break;
			}
		}

		return $allowed;
	}

	protected function _authorizeByContent($user, CakeRequest $request, $id) {
		if (!isset($this->settings['actionMap'][$request->params['action']])) {
			throw new CakeException(
				__('_authorizeByContent() - Access of un-mapped action "%1$s" in controller "%2$s"',
				$request->action,
				$request->controller
			));
		}

		list($plugin, $userModel) = pluginSplit($this->settings['userModel']);
		$user = array($userModel => $user);
		$acoNode = array(
			'model' => $this->_Controller->modelClass,
			'foreign_key' => $id,
		);
		$alias = sprintf('%s.%s', $acoNode['model'], $acoNode['foreign_key']);
		$action = $this->settings['actionMap'][$request->params['action']];

		$cacheName = 'permissions_content_' . strval($user['User']['id']);
		if (($permissions = Cache::read($cacheName, 'permissions')) === false) {
			$permissions = array();
			Cache::write($cacheName, $permissions, 'permissions');
		}

		if (!isset($permissions[$alias][$action])) {
			$Acl = $this->_Collection->load('Acl');
			$allowed = $Acl->check($user, $acoNode, $action);
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
			CakeLog::write(LOG_ERR, $user['User']['username'] . ' - ' . $action . '/' . $id . $status . $cached);
		}
		return $allowed;
	}

}
