<?php
class AclAro extends AppModel {

	public $name = 'AclAro';

	public $useTable = 'aros';

	public $actsAs = array('Tree');
	
/** Generate allowed actions for current logged in Role
 *
 * @return array of elements formatted like ControllerName/action_name
 */
	function getAllowedActionsByRoleId($roleId) {
		$Aco = ClassRegistry::init('Acl.AclAco');
		$Aro = ClassRegistry::init('Acl.AclAro');
		$ArosAco = ClassRegistry::init('Acl.ArosAco');
		$acosTree = $Aco->generateTreeList(array(
			'AclAco.parent_id !=' => null,
		), '{n}.AclAco.id', '{n}.AclAco.alias');
		$acos = array();
		$controller = null;
		foreach ($acosTree AS $acoId => $acoAlias) {
			if (substr($acoAlias, 0, 1) == '_') {
				$acos[$acoId] = $controller . '/' . substr($acoAlias, 1);
			} else {
				$controller = $acoAlias;
			}
		}
		$acoIds = array_keys($acos);

		$aro = $Aro->find('first', array(
			'conditions' => array(
				'AclAro.model' => 'Role',
				'AclAro.foreign_key' => $roleId,
			),
		));
		$aroId = $aro['AclAro']['id'];

		$permissionsForCurrentRole = $ArosAco->find('list', array(
			'conditions' => array(
				'ArosAco.aro_id' => $aroId,
				'ArosAco.aco_id' => $acoIds,
				'ArosAco._create' => 1,
				'ArosAco._read' => 1,
				'ArosAco._update' => 1,
				'ArosAco._delete' => 1,
			),
			'fields' => array(
				'ArosAco.id',
				'ArosAco.aco_id',
			),
		));
		$permissionsByActions = array();
		foreach ($permissionsForCurrentRole AS $acoId) {
			$permissionsByActions[] = $acos[$acoId];
		}

		return $permissionsByActions;
	}

}
