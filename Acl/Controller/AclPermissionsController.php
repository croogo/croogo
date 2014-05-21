<?php

namespace Croogo\Acl\Controller;
App::uses('AclAppController', 'Acl.Controller');

/**
 * AclPermissions Controller
 *
 * @category Controller
 * @package  Croogo.Acl
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
		'Acl.AclPermission',
		'Acl.AclAco',
		'Acl.AclAro',
		'Users.Role',
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
 * @param id integer aco id, when null, the root ACO is used
 * @return void
 */
	public function admin_index($id = null, $level = null) {
		$this->set('title_for_layout', __d('croogo', 'Permissions'));
		if (isset($this->request->query['root'])) {
			$query = strtolower($this->request->query['root']);
		}

		if ($id == null) {
			$root = isset($query) ? $query : 'controllers';
			$root = $this->AclAco->node(str_replace('.', '_', $root));
			$root = $root[0];
		} else {
			$root = $this->AclAco->read(null, $id);
		}

		if ($level !== null) {
			$level++;
		}

		$acos = array();
		$roles = $this->Role->find('list');
		if ($root) {
			$acos = $this->AclAco->getChildren($root['Aco']['id']);
		}
		$this->set(compact('acos', 'roles', 'level'));

		$aros = $this->AclAro->getRoles($roles);
		if ($root && $this->RequestHandler->ext == 'json') {
			$options = array_intersect_key(
				$this->request->query,
				array('perms' => null, 'urls' => null)
			);
			$cacheName = 'permissions_aco_' . $root['Aco']['id'];
			$permissions = Cache::read($cacheName, 'permissions');
			if ($permissions === false) {
				$permissions = $this->AclPermission->format($acos, $aros, $options);
				Cache::write($cacheName, $permissions, 'permissions');
			}
		} else {
			$permissions = array();
		}

		$this->set(compact('aros', 'permissions'));

		if ($this->request->is('ajax') && isset($query)) {
			$this->render('Acl.Elements/admin/acl_permissions_table');
		} else {
			$this->_setPermissionRoots();
		}
	}

	protected function _setPermissionRoots() {
		$roots = $this->AclAco->getPermissionRoots();
		foreach ($roots as $id => $root) {
			Croogo::hookAdminTab(
				'AclPermissions/admin_index',
				__d('croogo', $root['Aco']['title']),
				'Croogo.blank',
				array(
					'linkOptions' => array(
						'data-alias' => $root['Aco']['alias'],
					),
				)
			);
		}
		$this->set(compact('roots'));
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
			return $this->redirect(array('action' => 'index'));
		}

		// see if acoId and aroId combination exists
		$this->AclPermission->Aro->id = $aroId;
		$aro = $this->AclPermission->Aro->read();
		$aro = $aro['Aro'];
		$path = $this->AclPermission->Aco->getPath($acoId);
		$path = join('/', Hash::extract($path, '{n}.Aco.alias'));

		$permitted = !$this->AclPermission->check($aro, $path);
		$success = $this->AclPermission->allow($aro, $path, '*', $permitted ? 1 : -1);
		if ($success) {
			$this->AclPermission->Aco->id = $acoId;
			$parentAcoId = $this->AclPermission->Aco->field('parent_id');
			$cacheName = 'permissions_aco_' . $parentAcoId;
			Cache::delete($cacheName, 'permissions');
			Cache::delete('permissions_public', 'permissions');
		}

		$this->set(compact('acoId', 'aroId', 'data', 'success', 'permitted'));
	}

/**
 * admin_upgrade
 *
 * upgrades ACL database
 * @return void
 */
	public function admin_upgrade() {
		App::uses('AclUpgrade', 'Acl.Lib');
		$AclUpgrade = new AclUpgrade();
		$result = $AclUpgrade->upgrade();
		if ($result === true) {
			$this->Session->delete(AuthComponent::$sessionKey . '.aclUpgrade');
			$this->Session->setFlash(__d('croogo', 'ACL database has been upgraded successfully'), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(join('<br>', $result), 'default', array('class' => 'error'));
		}
		return $this->redirect($this->referer());
	}

}
