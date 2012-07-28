<?php

class RowLevelAclComponent extends Component {

	protected $_controller;

/**
 * startup
 */
	public function startup(Controller $controller) {
		$this->_controller = $controller;
		$rowLevel = Configure::read('Access Control.rowLevel');
		if ($rowLevel && !empty($controller->request->params['pass'][0])) {
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
		$roles = $this->_controller->Node->User->Role->find('list', array(
			'cache' => array('name' => 'roles', 'config' => 'nodes_index'),
		));
		$modelClass = $this->_controller->modelClass;
		$foreignKey = $this->_controller->{$modelClass}->primaryKey;
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
