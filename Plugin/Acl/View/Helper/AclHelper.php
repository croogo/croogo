<?php
/**
 * Acl Helper
 *
 * @category Helper
 * @package  Croogo
 * @version  1.4
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclHelper extends Helper {

/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
	public $helpers = array();

/**
 * Cached actions per Role
 *
 * @var array
 * @access public
 */
	public $allowedActions = array();

/**
 * beforeRender
 *
 */
	public function beforeRender($viewFile) {
		// display upgrade link when required
		$key = AuthComponent::$sessionKey . '.aclUpgrade';
		if ($this->_View->Session->read($key)) {
			$link = $this->_View->Html->link(
				__('Upgrade Acl database'),
				array('controller' => 'acl_permissions', 'action' => 'upgrade'),
				array('button' => 'primary')
			);
			$this->_View->Blocks->append('actions', sprintf('<li>%s</li>', $link));
		}
	}

/**
 * Generate allowed actions for current logged in Role
 *
 * @return array
 */
	public function getAllowedActionsByRoleId($roleId) {
		if (!empty($this->allowedActions[$roleId])) {
			return $this->allowedActions[$roleId];
		}

		$this->allowedActions[$roleId] = ClassRegistry::init('Acl.AclPermission')->getAllowedActionsByRoleId($roleId);
		return $this->allowedActions[$roleId];
	}

/**
 * Check if url is allowed for the Role
 *
 * @return boolean
 */
	public function linkIsAllowedByRoleId($roleId, $url) {
		if (isset($url['admin']) && $url['admin'] == true) {
			$url['action'] = 'admin_' . $url['action'];
		}
		$plugin = empty($url['plugin']) ? null : Inflector::camelize($url['plugin']) . '/';
		$path = '/:plugin/:controller/:action';
		$path = str_replace(
			array(':controller', ':action', ':plugin/'),
			array(Inflector::camelize($url['controller']), $url['action'], $plugin),
			'controllers/' . $path
			);
		$linkAction = str_replace('//', '/', $path);
		if (in_array($linkAction, $this->getAllowedActionsByRoleId($roleId))) {
			return true;
		}
		return false;
	}

/**
 * Generate allowed actions for current logged in User
 *
 * @return array
 */
	public function getAllowedActionsByUserId($roleId) {
		if (!empty($this->allowedActions[$roleId])) {
			return $this->allowedActions[$roleId];
		}

		$this->allowedActions[$roleId] = ClassRegistry::init('Acl.AclPermission')->getAllowedActionsByUserId($roleId);
		return $this->allowedActions[$roleId];
	}

/**
 * Check if url is allowed for the User
 *
 * @param integer $userId User Id
 * @param array|string $url link/url to check
 * @return boolean
 */
	public function linkIsAllowedByUserId($userId, $url) {
		if (is_array($url)) {
			if (isset($url['admin']) && $url['admin'] == true && strpos($url['action'], 'admin_') == -1) {
				$url['action'] = 'admin_' . $url['action'];
			}
			$plugin = empty($url['plugin']) ? null : Inflector::camelize($url['plugin']) . '/';
			$path = '/:plugin/:controller/:action';
			$path = str_replace(
				array(':controller', ':action', ':plugin/'),
				array(Inflector::camelize($url['controller']), $url['action'], $plugin),
				'controllers/' . $path
			);
		} else {
			$path = $url;
		}
		$linkAction = str_replace('//', '/', $path);
		if (in_array($linkAction, $this->getAllowedActionsByUserId($userId))) {
			return true;
		}
		return false;
	}

}
