<?php
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
class AclPermission extends AppModel {

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
 * belongsTo
 *
 * @var array
 */
	public $belongsTo = array(
		'AclAro' => array(
			'className' => 'Acl.AclAro',
			'foreignKey' => 'aro_id',
		),
		'AclAco' => array(
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
		$acosTree = $this->AclAco->generateTreeList(array(
			'AclAco.parent_id !=' => null,
		), '{n}.AclAco.id', '{n}.AclAco.alias');
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

		$aro = $this->AclAro->find('first', array(
			'conditions' => array(
				'AclAro.model' => 'Role',
				'AclAro.foreign_key' => $roleId,
			),
		));
		$aroId = $aro['AclAro']['id'];

		$permissionsForCurrentRole = $this->find('list', array(
			'conditions' => array(
				'AclPermission.aro_id' => $aroId,
				'AclPermission.aco_id' => $acoIds,
				'AclPermission._create' => 1,
				'AclPermission._read' => 1,
				'AclPermission._update' => 1,
				'AclPermission._delete' => 1,
			),
			'fields' => array(
				'AclPermission.id',
				'AclPermission.aco_id',
			),
		));
		$permissionsByActions = array();
		foreach ($permissionsForCurrentRole as $acoId) {
			$permissionsByActions[] = $acos[$acoId];
		}

		return $permissionsByActions;
	}

}
