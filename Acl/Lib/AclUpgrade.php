<?php

/**
 * Utility class to assist upgrading default aco records from 1.4 to 1.5
 *
 * @package Croogo.Acl.Lib
 */
class AclUpgrade extends Object {

/**
 * Map of new ACO hierarchy.
 */
	protected $_acoMap = array(
		'Acl' => array(
			'AclAcos',
			'AclActions', 'AclAros', 'AclPermissions',
			),
		'Blocks' => array(
			'Blocks', 'Regions',
			),
		'Extensions' => array(
			'ExtensionsHooks',
			'ExtensionsLocales', 'ExtensionsPlugins', 'ExtensionsThemes'
			),
		'FileManager' => array(
			'Attachments', 'FileManager',
			),
		'Comments' => array(
			'Comments',
			),
		'Contacts' => array(
			'Contacts', 'Messages',
			),
		'Nodes' => array(
			'Nodes',
			),
		'Menus' => array(
			'Links', 'Menus',
			),
		'Settings' => array(
			'Settings', 'Languages',
			),
		'Taxonomy' => array(
			'Terms', 'Types', 'Vocabularies',
			),
		'Users' => array(
			'Users', 'Roles',
			),
		);

	public function __construct() {
		$this->Permission = ClassRegistry::init('Acl.AclPermission');
		$this->Aco = $this->Permission->Aco;
		$this->Aro = $this->Permission->Aro;
	}

/**
 * Upgrade < 1.5 ACO database hierarchy
 * For core controllers, it will be moved accordingly under its respective
 * plugin as defined in $_acoMap
 *
 */
	public function upgrade() {
		$actionPath = 'controllers/';
		$root = $this->Aco->node(str_replace('/', '', $actionPath));
		if (empty($root)) {
			return __d('croogo', 'No root node found');
		} else {
			$root = $root[0];
		}

		$upgraded = $this->Aco->node('controllers/Nodes/Nodes/admin_index');
		$upgraded = !empty($upgraded);
		if ($upgraded) {
			return array(__d('croogo', '<warning>ACL Database seems to have already been upgraded</warning>'));
		}

		$this->Aco->begin();
		$this->_renameFileManagerAco();
		$errors = $this->update_role_hierarchy();
		foreach ($this->_acoMap as $plugin => $controllers) {
			$pluginPath = $actionPath . $plugin;
			$pluginNode = $this->Aco->node($pluginPath);
			if (empty($pluginNode)) {
				$this->Aco->create(array(
					'parent_id' => $root['Aco']['id'],
					'model' => null,
					'alias' => $plugin,
					));
				$pluginNode = $this->Aco->save();
				$pluginNode['Aco']['id'] = $this->Aco->id;
			} else {
				// controller with the same name already exists
				$controllerNode = $pluginNode;
				$this->Aco->create(array(
					'parent_id' => $root['Aco']['id'],
					'model' => null,
					'alias' => $plugin,
					));
				$pluginNode = $this->Aco->save();
				$controllerNode[0]['Aco']['parent_id'] = $pluginNode['Aco']['id'];
				$this->Aco->save($controllerNode[0]);
			}
			foreach ($controllers as $controllerName) {
				$controllerPath = $actionPath . $controllerName;
				$controllerNode = $this->Aco->node($controllerPath);
				if ($controllerNode) {
					$controllerNode = $controllerNode[0];
					$controllerNode['Aco']['parent_id'] = $pluginNode['Aco']['id'];
					$this->Aco->save($controllerNode);
				} else {
					$correctControllerPath = $actionPath . $plugin . '/' . $controllerName;
					$correctControllerNode = $this->Aco->node($correctControllerPath);
					if (empty($correctControllerNode)) {
						$errors[] = __d('croogo', '%s not found', $controllerPath);
					}
				}
			}
		}
		if (!empty($errors)) {
			$this->Aco->rollback();
			return $errors;
		}
		$this->Aco->commit();
		return true;
	}

/**
 * Setup role hierarchy
 */
	public function update_role_hierarchy() {
		$errors = array();

		$admin = $this->Aro->node(array('model' => 'Role', 'foreign_key' => 1));
		$registered = $this->Aro->node(array('model' => 'Role', 'foreign_key' => 2));
		$public = $this->Aro->node(array('model' => 'Role', 'foreign_key' => 3));

		if (empty($public)) {
			$errors[] = __d('croogo', 'Role: Public not found');
		}
		if ($registered) {
			$registered[0]['Aro']['parent_id'] = $public[0]['Aro']['id'];
			$this->Aro->save($registered[0]);
		} else {
			$errors[] = __d('croogo', 'Role: Registered not found');
		}
		if ($admin) {
			$admin[0]['Aro']['parent_id'] = $registered[0]['Aro']['id'];
			$this->Aro->save($admin[0]);
		} else {
			$errors[] = __d('croogo', 'Role: Admin not found');
		}
		return array();
	}

	protected function _renameFileManagerAco() {
		$aco = $this->Aco->findByAlias('Filemanager');
		$aco['Aco']['alias'] = 'FileManager';
		$this->Aco->save($aco);
	}

}
