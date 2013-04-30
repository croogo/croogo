<?php

/**
 * RoleLevelAcl Component
 *
 * When "Access Control.rowLevel" Setting is active, this component will perform
 * the necesary setup on controller's primary model and hook the element for 
 * backend use.
 *
 * You can also use it to configure the action mappings used by AclCachedAuthorize
 * class, for example:
 *
 * ```
 *      class ItemsController extends AppController {
 *          public $components = array(
 *              'RowLevelAcl' => array(
 *                  'className' => 'Acl.RowLevelAcl',
 *                  'settings' => array(
 *                      'actionMap' => array(
 *                          'admin_reserve' => 'update', // action map
 *                      ),
 *                  ),
 *              ));
 *      }
 * ```
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
class RowLevelAclComponent extends Component {

/**
 * controller instance
 */
	protected $_controller;

/**
 * initialize
 *
 * attaches Acl and RowLevelAcl behavior to the controller's primary model and
 * hook the appropriate admin tabs
 */
	public function initialize(Controller $controller) {
		$this->_controller = $controller;
		$Model = $controller->{$controller->modelClass};
		$Model->Behaviors->load('Acl', array(
			'className' => 'Croogo.CroogoAcl', 'type' => 'controlled',
		));
		$Model->Behaviors->load('RowLevelAcl', array(
			'className' => 'Acl.RowLevelAcl'
		));

		$name = $controller->name;
		$element = 'Acl.admin/row_acl';
		if (!empty($this->settings['adminTabElement'])) {
			$element = $this->settings['adminTabElement'];
		}
		$adminTabActions = array('admin_add', 'admin_edit');
		if (!empty($this->settings['adminTabActions'])) {
			$adminTabActions += $this->settings['adminTabActions'];
		}
		foreach ($adminTabActions as $action) {
			Croogo::hookAdminTab("$name/$action", __d('croogo', 'Permissions'), $element);
		}
	}

/**
 * startup
 */
	public function startup(Controller $controller) {
		if (!empty($controller->request->params['pass'][0])) {
			$id = $controller->request->params['pass'][0];
			$this->_rolePermissions($id);
		}
	}

/**
 * Retrieve a list of roles with access status for a given node
 *
 * @param integer $id Node id
 * @return array 
 */
	protected function _rolePermissions($id) {
		$Permission = $this->_controller->Acl->Aro->Permission;
		$Role = ClassRegistry::init('Users.Role');
		$roles = $Role->find('list', array(
			'cache' => array('name' => 'roles', 'config' => 'permissions'),
		));
		$modelClass = $this->_controller->modelClass;
		$aco = array('model' => $modelClass, 'foreign_key' => $id);
		foreach ($roles as $roleId => $role) {
			$aro = array('model' => 'Role', 'foreign_key' => $roleId);
			try {
				$allowed = $Permission->check($aro, $aco);
			} catch (CakeException $e) {
				$allowed = false;
			}
			$rolePermissions[] = array(
				'Role' => array(
					'id' => $roleId, 'title' => $role, 'allowed' => $allowed
				)
			);
		}
		$this->_controller->set(compact('rolePermissions'));
	}

}
