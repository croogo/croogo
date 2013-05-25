<?php

App::uses('Component', 'Controller');

/**
 * AclAccess Component provides * various methods to manipulate Aros and Acos,
 * and additionaly setup various settings for backend/admin use.
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
class AclAccessComponent extends Component {

/**
 * _controller
 *
 * @var Controller
 */
	protected $_controller = null;

/**
 * startup
 *
 * @param Controller $controller
 */
	public function startup(Controller $controller) {
		$this->_controller = $controller;
		$adminPrefix = isset($controller->request->params['admin']);
		if (!$adminPrefix) {
			return;
		}

		switch ($controller->name) {
			case 'Roles':
				$this->_setupRole();
			break;
			case 'AclActions':
			case 'AclPermissions':
				$this->_checkUpgrade();
			break;
		}
	}

/**
 * Hook admin menu element to set role parent
 */
	protected function _setupRole() {
		$title = __d('croogo', 'Parent Role');
		$element = 'Acl.admin/parent_role';
		Croogo::hookAdminTab('Roles/admin_add', $title, $element);
		Croogo::hookAdminTab('Roles/admin_edit', $title, $element);

		$this->_controller->Role->bindAro();
		$id = null;
		if (!empty($this->_controller->request->params['pass'][0])) {
			$id = $this->_controller->request->params['pass'][0];
		}
		$this->_controller->set('parents', $this->_controller->Role->allowedParents($id));
	}

/**
 * checks wether ACL upgrade is required
 * writes a session variable that will be picked up by the AclHelper
 */
	protected function _checkUpgrade() {
		$key = AuthComponent::$sessionKey . '.aclUpgrade';
		if ($this->_controller->Session->check($key)) {
			$upgrade = $this->_controller->Session->read($key);
			if ($upgrade) {
				$this->_controller->helpers[] = 'Acl.Acl';
			}
			return;
		}
		$node = $this->_controller->Acl->Aco->node('controllers/Nodes/admin_index');
		$this->_controller->Session->write($key, !empty($node));
		$this->_controller->helpers[] = 'Acl.Acl';
	}

/**
 * Add ACO
 *
 * Creates ACOs with permissions for roles.
 *
 * Action Path format:
 * - ControllerName
 * - ControllerName/method_name
 *
 * @param string $action action path
 * @param array $allowRoles Role aliases
 * @return void
 */
	public function addAco($action, $allowRoles = array()) {
		$actionPath = $this->_controller->Auth->authorize['all']['actionPath'];
		if (strpos($action, $actionPath) === false) {
			$action = str_replace('//', '/', $actionPath . '/' . $action);
		}
		$Aco = ClassRegistry::init('Acl.AclAco');
		$Aco->addAco($action, $allowRoles);
	}

/**
 * Remove ACO
 *
 * Removes ACOs and their Permissions
 *
 * Action Path format:
 * - ControllerName
 * - ControllerName/method_name
 *
 * @param string $action action path
 * @return void
 */
	public function removeAco($action) {
		$actionPath = $this->_controller->Auth->authorize['all']['actionPath'];
		if (strpos($action, $actionPath) === false) {
			$action = str_replace('//', '/', $actionPath . '/' . $action);
		}
		$Aco = ClassRegistry::init('Acl.AclAco');
		$Aco->removeAco($action);
	}

}
