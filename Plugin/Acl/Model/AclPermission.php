<?php

App::uses('Permission', 'Model');

/**
 * AclPermission Model
 *
 * PHP version 5
 *
 * @category Model
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclPermission extends Permission {

/**
 * name
 *
 * @var string
 */
	public $name = 'AclPermission';

/**
 * useTable
 *
 * @var string
 */
	public $useTable = 'aros_acos';

/**
 * alias
 */
	public $alias = 'Permission';

/**
 * belongsTo
 *
 * @var array
 */
	public $belongsTo = array(
		'Aro' => array(
			'className' => 'Acl.AclAro',
			'foreignKey' => 'aro_id',
		),
		'Aco' => array(
			'className' => 'Acl.AclAco',
			'foreignKey' => 'aco_id',
		),
	);

/**
 * Generate allowed actions for current logged in Role
 *
 * @param integer $roleId
 * @return array of elements formatted like ControllerName/action_name
 */
	public function getAllowedActionsByRoleId($roleId) {
		$acosTree = $this->Aco->generateTreeList(array(
			'Aco.parent_id !=' => null,
			), '{n}.Aco.id', '{n}.Aco.alias');
		$acos = array();
		$controller = null;
		foreach ($acosTree as $acoId => $acoAlias) {
			if (substr($acoAlias, 0, 1) == '_') {
				$acos[$acoId] = $controller . '/' . substr($acoAlias, 1);
			} else {
				$controller = $acoAlias;
			}
		}
		$acoIds = array_keys($acos);

		$aro = $this->Aro->find('first', array(
			'conditions' => array(
				'Aro.model' => 'Role',
				'Aro.foreign_key' => $roleId,
			),
		));
		$aroId = $aro['Aro']['id'];

		$permissionsForCurrentRole = $this->find('list', array(
			'conditions' => array(
				'Permission.aro_id' => $aroId,
				'Permission.aco_id' => $acoIds,
				'Permission._create' => 1,
				'Permission._read' => 1,
				'Permission._update' => 1,
				'Permission._delete' => 1,
			),
			'fields' => array(
				'Permission.id',
				'Permission.aco_id',
			),
		));
		$permissionsByActions = array();
		foreach ($permissionsForCurrentRole as $acoId) {
			$permissionsByActions[] = $acos[$acoId];
		}

		return $permissionsByActions;
	}

/**
 * Retrieve an array for formatted aros/aco data
 *
 * @param array $acos
 * @param array $aros
 * @param array $options
 * @return array formatted array
 */
	public function format($acos, $aros, $options = array()) {
		$options = Hash::merge(array(
			'model' => 'Role',
			'perms' => true
			), $options);
		extract($options);
		$permissions = array();

		foreach ($acos as $index => $aco) {
			$acoId = $aco['Aco']['id'];
			$acoAlias = $aco['Aco']['alias'];

			$path = $this->Aco->getPath($acoId);
			$path = join('/', Hash::extract($path, '{n}.Aco.alias'));
			$data = array(
				'children' => $this->Aco->childCount($acoId, true),
				'depth' => substr_count($path, '/'),
				);

			foreach ($aros as $aroFk => $aroId) {
				$role = array(
					'model' => $model, 'foreign_key' => $aroFk,
				);
				if ($perms) {
					if ($aroFk == 1 || $this->check($role, $path)) {
						$data['roles'][$aroFk] = 1;
					} else {
						$data['roles'][$aroFk] = 0;
					}
				}
				$permissions[$acoId] = array($acoAlias => $data);
			}

		}
		return $permissions;
	}

}
