<?php

class AclUpgradeComponent extends Component {

	var $__acosToMove = array(
		'Acl' => array('AclActions', 'AclAros', 'AclPermissions'),
		'Extensions' => array('ExtensionsLocales', 'ExtensionsPlugins', 'ExtensionsThemes'),
		);

	var $controller = false;

	function initialize(&$controller) {
		$this->controller =& $controller;
	}

	function upgrade() {
		$controller =& $this->controller;
		$Auth =& $controller->Auth;
		$Aco =& $controller->Acl->Aco;
		$actionPath = $Auth->authorize[AuthComponent::ALL]['actionPath'] . '/';

		$root = $Aco->node(str_replace('/', '', $actionPath));
		if (empty($root)) {
			return __('No root node found');
		} else {
			$root = $root[0];
		}

		$Aco->begin();
		$errors = $this->update_role_hierarchy();
		foreach ($this->__acosToMove as $plugin => $controllers) {
			$pluginPath = $actionPath . $plugin;
			$pluginNode = $Aco->node($pluginPath);
			if (empty($pluginNode)) {
				$Aco->create(array(
					'parent_id' => $root['Aco']['id'],
					'model' => null,
					'alias' => $plugin,
					));
				$pluginNode = $Aco->save();
				$pluginNode['Aco']['id'] = $Aco->id;
			} else {
				$pluginNode = $pluginNode[0];
			}
			foreach ($controllers as $controllerName) {
				$controllerPath = $actionPath . $controllerName;
				$controllerNode = $Aco->node($controllerPath);
				if ($controllerNode) {
					$controllerNode = $controllerNode[0];
					$controllerNode['Aco']['parent_id'] = $pluginNode['Aco']['id'];
					$Aco->save($controllerNode);
				} else {
					$correctControllerPath = $actionPath . $plugin . '/' . $controllerName;
					$correctControllerNode = $Aco->node($correctControllerPath);
					if (empty($correctControllerNode)) {
						$errors[] = __('%s not found', $controllerPath);
					}
				}
			}
		}
		if (!empty($errors)) {
			$Aco->rollback();
			return $errors;
		}
		$Aco->commit();
		return true;
	}

	function update_role_hierarchy() {
		$controller = $this->controller;
		$Aro =& $controller->Acl->Aro;
		$errors = array();

		$admin = $Aro->node(array('model' => 'Role', 'foreign_key' => 1));
		$registered = $Aro->node(array('model' => 'Role', 'foreign_key' => 2));
		$public = $Aro->node(array('model' => 'Role', 'foreign_key' => 3));

		if (empty($public)) {
			$errors[] = __('Role: Public not found');
		}
		if ($registered) {
			$registered[0]['Aro']['parent_id'] = $public[0]['Aro']['id'];
			$Aro->save($registered[0]);
		} else {
			$errors[] = __('Role: Registered not found');
		}
		if ($admin) {
			$admin[0]['Aro']['parent_id'] = $registered[0]['Aro']['id'];
			$Aro->save($admin[0]);
		} else {
			$errors[] = __('Role: Admin not found');
		}
		return array();
	}
}
