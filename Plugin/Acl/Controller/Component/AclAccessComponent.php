<?php

/**
 * AclAccess Component
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
		$isAdminAction = $controller->name == 'Roles' && $adminPrefix;
		if (!$adminPrefix) {
			return;
		}

		switch ($controller->name) {
			case 'Roles':
				$this->_setupRole();
			break;
			case 'AclPermissions':
				$this->_checkUpgrade();
			break;
		}
	}

/**
 * Hook admin menu element to set role parent
 */
	protected function _setupRole() {
		$title = __('Parent Role');
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
		$node = $this->_controller->Acl->Aco->node('controllers/Nodes');
		$this->_controller->Session->write($key, !empty($node));
		$this->_controller->helpers[] = 'Acl.Acl';
	}

/**
 * ACL: add ACO
 *
 * Creates ACOs with permissions for roles.
 *
 * @param string $action possible values: ControllerName, ControllerName/method_name
 * @param array $allowRoles Role aliases
 * @return void
 */
	public function addAco($action, $allowRoles = array()) {
		// AROs
		$aroIds = array();
		if (count($allowRoles) > 0) {
			$roles = ClassRegistry::init('Users.Role')->find('list', array(
				'conditions' => array(
					'Role.alias' => $allowRoles,
				),
				'fields' => array(
					'Role.id',
					'Role.alias',
				),
			));
			$roleIds = array_keys($roles);
			$aros = $this->_controller->Acl->Aro->find('list', array(
				'conditions' => array(
					'Aro.model' => 'Role',
					'Aro.foreign_key' => $roleIds,
				),
				'fields' => array(
					'Aro.id',
					'Aro.alias',
				),
			));
			$aroIds = array_keys($aros);
		}

		// ACOs
		$acoNode = $this->_controller->Acl->Aco->node($this->_controller->Auth->authorize['all']['actionPath'] . $action);
		if (!isset($acoNode['0']['Aco']['id'])) {
			if (!strstr($action, '/')) {
				$parentNode = $this->_controller->Acl->Aco->node(str_replace('/', '', $this->_controller->Auth->authorize['all']['actionPath']));
				$alias = $action;
			} else {
				$actionE = explode('/', $action);
				$controllerName = $actionE['0'];
				$method = $actionE['1'];
				$alias = $method;
				$parentNode = $this->_controller->Acl->Aco->node($this->_controller->Auth->authorize['all']['actionPath'] . '/' . $controllerName);
			}
			$parentId = $parentNode['0']['Aco']['id'];
			$acoData = array(
				'parent_id' => $parentId,
				'model' => null,
				'foreign_key' => null,
				'alias' => $alias,
			);
			$this->_controller->Acl->Aco->id = false;
			$this->_controller->Acl->Aco->save($acoData);
			$acoId = $this->_controller->Acl->Aco->id;
		} else {
			$acoId = $acoNode['0']['Aco']['id'];
		}

		// Permissions (aros_acos)
		foreach ($aroIds as $aroId) {
			$permission = $this->_controller->Acl->Aro->Permission->find('first', array(
				'conditions' => array(
					'Permission.aro_id' => $aroId,
					'Permission.aco_id' => $acoId,
				),
			));
			if (!isset($permission['Permission']['id'])) {
				// create a new record
				$permissionData = array(
					'aro_id' => $aroId,
					'aco_id' => $acoId,
					'_create' => 1,
					'_read' => 1,
					'_update' => 1,
					'_delete' => 1,
				);
				$this->_controller->Acl->Aco->Permission->id = false;
				$this->_controller->Acl->Aco->Permission->save($permissionData);
			} else {
				// check if not permitted
				if ($permission['Permission']['_create'] == 0 ||
					$permission['Permission']['_read'] == 0 ||
					$permission['Permission']['_update'] == 0 ||
					$permission['Permission']['_delete'] == 0) {
					$permissionData = array(
						'id' => $permission['Permission']['id'],
						'aro_id' => $aroId,
						'aco_id' => $acoId,
						'_create' => 1,
						'_read' => 1,
						'_update' => 1,
						'_delete' => 1,
					);
					$this->_controller->Acl->Aco->Permission->id = $permission['Permission']['id'];
					$this->_controller->Acl->Aco->Permission->save($permissionData);
				}
			}
		}
	}

/**
 * ACL: remove ACO
 *
 * Removes ACOs and their Permissions
 *
 * @param string $action possible values: ControllerName, ControllerName/method_name
 * @return void
 */
	public function removeAco($action) {
		$acoNode = $this->_controller->Acl->Aco->node($this->_controller->Auth->authorize['all']['actionPath'] . '/' . $action);
		if (isset($acoNode['0']['Aco']['id'])) {
			$this->_controller->Acl->Aco->delete($acoNode['0']['Aco']['id']);
		}
	}

}
