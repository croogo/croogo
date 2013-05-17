<?php

App::uses('AclNode', 'Model');

/**
 * AclAco Model
 *
 * PHP version 5
 *
 * @category Model
 * @package  Croogo.Acl.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclAco extends AclNode {

/**
 * name
 *
 * @var string
 */
	public $name = 'AclAco';

/**
 * useTable
 *
 * @var string
 */
	public $useTable = 'acos';

/**
 * actsAs
 *
 * @var array
 */
	public $actsAs = array('Tree');

/**
 * alias
 *
 */
	public $alias = 'Aco';

/**
 * hasAndBelongsToMany
 */
	public $hasAndBelongsToMany = array(
		'Aro' => array(
			'with' => 'Acl.AclPermission',
		),
	);

/**
 * getChildren
 *
 * @param integer aco id
 */
	public function getChildren($acoId, $fields = array()) {
		$fields = Hash::merge(array('id', 'parent_id', 'alias'), $fields);
		$acos = $this->children($acoId, true, $fields);
		foreach ($acos as &$aco) {
			$aco[$this->alias]['children'] = $this->childCount($aco[$this->alias]['id'], true);
		}
		return $acos;
	}

/**
 * Create ACO tree
 */
	public function createFromPath($path) {
		$pathE = explode('/', $path);
		$parent = $current = null;
		foreach ($pathE as $alias) {
			$current[] = $alias;
			$node = $this->node(join('/', $current));
			if ($node) {
				$parent = $node[0];
			} else {
				$aco = $this->create(array(
					'parent_id' => $parent['Aco']['id'],
					'alias' => $alias,
				));
				$parent = $this->save($aco);
			}
		}
		return $parent;
	}

/**
 * ACL: add ACO
 *
 * Creates ACOs with permissions for roles.
 *
 * @param string $action possible values: Controller, Controller/action,
 *                                        Plugin/Controller/action
 * @param array $allowRoles Role aliases
 * @return void
 */
	public function addAco($action, $allowRoles = array()) {
		// AROs
		$roles = array();
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
		}

		$this->createFromPath($action);
		$Permission = ClassRegistry::init('Acl.AclPermission');
		foreach ($roles as $roleId => $roleAlias) {
			$Permission->allow(array('model' => 'Role', 'foreign_key' => $roleId), $action);
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
		$acoNode = $this->node($action);
		if (isset($acoNode['0']['Aco']['id'])) {
			$this->delete($acoNode['0']['Aco']['id']);
		}
	}

}
