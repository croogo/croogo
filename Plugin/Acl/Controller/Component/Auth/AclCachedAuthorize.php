<?php
/**
 * AclCachedAuthorize
 *
 * PHP version 5
 *
 * @package  Croogo
 * @since    1.4
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
App::uses('BaseAuthorize', 'Controller/Component/Auth');

class AclCachedAuthorize extends BaseAuthorize {

	public function authorize($user, CakeRequest $request) {
		$allowed = false;
		$Acl = $this->_Collection->load('Acl');
		$user = array($this->settings['userModel'] => $user);
		$action = $this->action($request);

		$cacheName = 'permissions_' . strval($user['User']['id']);
		if (($permissions = Cache::read($cacheName, 'permissions')) === false) {
			$permissions = array();
			Cache::write($cacheName, $permissions, 'permissions');
		}

		if (!isset($permissions[$action])) {
			$allowed = $Acl->check($user, $action);
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
			CakeLog::write(LOG_ERROR, $user['User']['username'] . ' - ' . $action . $status . $cached);
		}

		return $allowed;
	}

}
