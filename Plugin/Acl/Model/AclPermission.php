<?php

App::uses('Permission', 'Model');

/**
 * AclPermission Model
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
 * afterSave
 */
	public function afterSave($created) {
		Cache::clearGroup('acl', 'permissions');
	}

/**
 * Generate allowed actions for current logged in Role
 *
 * @param integer $roleId
 * @return array of elements formatted like ControllerName/action_name
 */
	public function getAllowedActionsByRoleId($roleId) {
		$aro = $this->Aro->node(array(
			'model' => 'Role',
			'foreign_key' => $roleId,
		));
		if (empty($aro[0]['Aro']['id'])) {
			return array();
		}
		$aroId = $aro[0]['Aro']['id'];

		$permissionsForCurrentRole = $this->find('list', array(
			'conditions' => array(
				'Permission.aro_id' => $aroId,
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
			$path = $this->Aco->getPath($acoId);
			$path = join('/', Hash::extract($path, '{n}.Aco.alias'));
			$permissionsByActions[] = $path;
		}

		return $permissionsByActions;
	}

/**
 * Generate allowed actions for current logged in User
 *
 * @param integer $userId
 * @return array of elements formatted like ControllerName/action_name
 */
	public function getAllowedActionsByUserId($userId) {
		$aro = $this->Aro->node(array(
			'model' => 'User',
			'foreign_key' => $userId,
		));
		if (empty($aro[0]['Aro']['id'])) {
			return array();
		}
		$aroIds = Hash::extract($aro, '{n}.Aro.id');
		if (Configure::read('Access Control.multiRole')) {
			$RolesUser = ClassRegistry::init('Users.RolesUser');
			$rolesAro = $RolesUser->getRolesAro($userId);
			$aroIds = array_unique(Hash::merge($aroIds, $rolesAro));
		}

		$permissionsForCurrentUser = $this->find('list', array(
			'conditions' => array(
				'Permission.aro_id' => $aroIds,
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
		foreach ($permissionsForCurrentUser as $acoId) {
			$path = $this->Aco->getPath($acoId);
			if (!$path) {
				continue;
			}
			$path = join('/', Hash::extract($path, '{n}.Aco.alias'));
			if (!in_array($path, $permissionsByActions)) {
				$permissionsByActions[] = $path;
			}
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

		foreach ($acos as $aco) {
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
