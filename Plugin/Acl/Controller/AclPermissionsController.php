<?php
/**
 * AclPermissions Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclPermissionsController extends AclAppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'AclPermissions';

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array(
		'Acl.AclAco',
		'Acl.AclAro',
		'Acl.AclPermission',
		'Role',
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Security->requirePost('admin_toggle');
		if ($this->action == 'admin_toggle') {
			$this->Security->csrfCheck = false;
		}
	}

/**
 * admin_index
 *
 * @return void
 */
	public function admin_index() {
		$this->set('title_for_layout', __('Permissions'));

		$acoConditions = array(
			'parent_id !=' => null,
			//'model' => null,
			'foreign_key' => null,
			'alias !=' => null,
		);
		$acos  = $this->Acl->Aco->generateTreeList($acoConditions, '{n}.Aco.id', '{n}.Aco.alias');
		$roles = $this->Role->find('list');
		$this->set(compact('acos', 'roles'));

		$rolesAros = $this->AclAro->find('all', array(
			'conditions' => array(
				'AclAro.model' => 'Role',
				'AclAro.foreign_key' => array_keys($roles),
				),
			));
		$rolesAros = Set::combine($rolesAros, '{n}.AclAro.foreign_key', '{n}.AclAro.id');

		$permissions = array(); // acoId => roleId => bool
		foreach ($acos as $acoId => $acoAlias) {
			if (substr_count($acoAlias, '_') != 0) {
				$permission = array();
				foreach ($roles as $roleId => $roleTitle) {
					$hasAny = array(
						'aco_id'  => $acoId,
						'aro_id'  => $rolesAros[$roleId],
						'_create' => 1,
						'_read'   => 1,
						'_update' => 1,
						'_delete' => 1,
					);
					if ($this->AclPermission->hasAny($hasAny)) {
						$permission[$roleId] = 1;
					} else {
						$permission[$roleId] = 0;
					}
					$permissions[$acoId] = $permission;
				}
			}
		}
		$this->set(compact('rolesAros', 'permissions'));
	}

/**
 * admin_toggle
 *
 * @param integer $acoId
 * @param integer $aroId
 * @return void
 */
	public function admin_toggle($acoId, $aroId) {
		if (!$this->RequestHandler->isAjax()) {
			$this->redirect(array('action' => 'index'));
		}

		// see if acoId and aroId combination exists
		$conditions = array(
			'AclPermission.aco_id' => $acoId,
			'AclPermission.aro_id' => $aroId,
		);
		if ($this->AclPermission->hasAny($conditions)) {
			$data = $this->AclPermission->find('first', array('conditions' => $conditions));
			if ($data['AclPermission']['_create'] == 1 &&
				$data['AclPermission']['_read'] == 1 &&
				$data['AclPermission']['_update'] == 1 &&
				$data['AclPermission']['_delete'] == 1) {
				// from 1 to 0
				$data['AclPermission']['_create'] = 0;
				$data['AclPermission']['_read'] = 0;
				$data['AclPermission']['_update'] = 0;
				$data['AclPermission']['_delete'] = 0;
				$permitted = 0;
			} else {
				// from 0 to 1
				$data['AclPermission']['_create'] = 1;
				$data['AclPermission']['_read'] = 1;
				$data['AclPermission']['_update'] = 1;
				$data['AclPermission']['_delete'] = 1;
				$permitted = 1;
			}
		} else {
			// create - CRUD with 1
			$data['AclPermission']['aco_id'] = $acoId;
			$data['AclPermission']['aro_id'] = $aroId;
			$data['AclPermission']['_create'] = 1;
			$data['AclPermission']['_read'] = 1;
			$data['AclPermission']['_update'] = 1;
			$data['AclPermission']['_delete'] = 1;
			$permitted = 1;
		}

		// save
		$success = 0;
		if ($this->AclPermission->save($data)) {
			$success = 1;
		}

		$this->set(compact('acoId', 'aroId', 'data', 'success', 'permitted'));
	}

}
