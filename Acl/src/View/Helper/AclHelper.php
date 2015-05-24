<?php

namespace Croogo\Acl\View\Helper;

use Cake\View\Helper;
/**
 * Acl Helper
 *
 * @category Helper
 * @package  Croogo.Acl
 * @version  1.4
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclHelper extends Helper {

/**
 * Cached actions per Role
 *
 * @var array
 * @access public
 */
	public $allowedActions = array();

/**
 * Path Whitelist
 */
	protected $_pathWhitelist = array('/', '#');

/**
 * Constructor
 */
	public function __construct(View $View, $settings = array()) {
		$settings = Hash::merge(array(
			'pathWhitelist' => $this->_pathWhitelist
		), $settings);
		parent::__construct($View, $settings);
		$plugin = Configure::read('Site.acl_plugin');
		/* TODO: App::uses('AclPermission', $plugin . '.Model'); */
		$this->AclPermission = ClassRegistry::init($plugin . '.AclPermission');
	}

/**
 * Checks whether path is in whitelist
 *
 * @param string $path Path
 * @return bool True if path is in the whitelist
 */
	protected function _isWhitelist($url) {
		return in_array($url, $this->settings['pathWhitelist']);
	}

/**
 * beforeRender
 *
 */
	public function beforeRender($viewFile) {
		// display upgrade link when required
		$key = AuthComponent::$sessionKey . '.aclUpgrade';
		if ($this->_View->Session->read($key)) {
			$link = $this->_View->Croogo->adminAction(
				__d('croogo', 'Upgrade Acl database'),
				array('controller' => 'acl_permissions', 'action' => 'upgrade'),
				array('button' => 'primary')
			);
			$this->_View->Blocks->append('actions', sprintf('<li>%s</li>', $link));
		}
	}

/**
 * Returns an array of allowed actions for current logged in Role
 *
 * @param integer $roleId Role id
 * @return array
 */
	public function getAllowedActionsByRoleId($roleId) {
		if (!empty($this->allowedActions[$roleId])) {
			return $this->allowedActions[$roleId];
		}

		$this->allowedActions[$roleId] = $this->AclPermission->getAllowedActionsByRoleId($roleId);
		return $this->allowedActions[$roleId];
	}

/**
 * Check if url is allowed for the Role
 *
 * @param integer $roleId Role id
 * @param $url array
 * @return boolean
 */
	public function linkIsAllowedByRoleId($roleId, $url) {
		if (is_string($url)) {
			return $this->_isWhitelist($url);
		}
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
 * Returns an array of allowed actions for current logged in User
 *
 * @param integer $userId Role id
 * @return array
 */
	public function getAllowedActionsByUserId($userId) {
		if (!empty($this->allowedActions[$userId])) {
			return $this->allowedActions[$userId];
		}

		$this->allowedActions[$userId] = $this->AclPermission->getAllowedActionsByUserId($userId);
		return $this->allowedActions[$userId];
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
			if (isset($url['admin']) && $url['admin'] == true && strpos($url['action'], 'admin_') === false) {
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
			if ($this->_isWhitelist($url)) {
				return true;
			}
			$path = $url;
		}
		$linkAction = str_replace('//', '/', $path);

		if (in_array($linkAction, $this->getAllowedActionsByUserId($userId))) {
			return true;
		} else {
			$userAro = array('model' => 'User', 'foreign_key' => $userId);
			$nodes = $this->AclPermission->Aro->node($userAro);
			if (isset($nodes[0]['Aro'])) {
				if ($this->AclPermission->check($nodes[0]['Aro'], $linkAction)) {
					return true;
				}
			}
		}
		return false;
	}

}
