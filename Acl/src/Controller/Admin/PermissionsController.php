<?php

namespace Croogo\Acl\Controller\Admin;

use Acl\Controller\AclAppController;
use Acl\Lib\AclUpgrade;
use Cake\Cache\Cache;
use Cake\Event\Event;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\ORM\TableRegistry;
use Croogo\Croogo\Controller\CroogoAppController;
use Croogo\Croogo\Croogo;

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
class PermissionsController extends CroogoAppController {

	private $Acos;
	private $Aros;
	private $Roles;
	private $Permissions;

	public function initialize() {
		parent::initialize();

		$this->Acos = TableRegistry::get('Croogo/Acl.Acos');
		$this->Aros = TableRegistry::get('Croogo/Acl.Aros');
		$this->Roles = TableRegistry::get('Croogo/Users.Roles');
		$this->Permissions = TableRegistry::get('Croogo/Acl.Permissions');
	}

	/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter(Event $event) {
		parent::beforeFilter($event);
		if ($this->request->action == 'toggle') {
			if (!$this->request->is('post')) {
				throw new MethodNotAllowedException();
			}
			$this->Security->csrfCheck = false;
		}
	}

/**
 * admin_index
 *
 * @param id integer aco id, when null, the root ACO is used
 * @return void
 */
	public function index($id = null, $level = null) {
		if (isset($this->request->query['root'])) {
			$query = strtolower($this->request->query('root'));
		}

		if ($id == null) {
			$root = isset($query) ? $query : 'controllers';
			$root = $this->Acos->node(str_replace('.', '_', $root));
			$root = $root->firstOrFail();
		} else {
			$root = $this->Acos->get($id);
		}

		if ($level !== null) {
			$level++;
		}

		$acos = array();
		$roles = $this->Roles->find('list');
		if ($root) {
			$acos = $this->Acos->getChildren($root->id);
		}
		$this->set(compact('acos', 'roles', 'level'));

		$aros = $this->Aros->getRoles($roles);
		if ($root && $this->RequestHandler->ext == 'json') {
			$options = array_intersect_key(
				$this->request->query,
				array('perms' => null, 'urls' => null)
			);
			$cacheName = 'permissions_aco_' . $root['Aco']['id'];
			$permissions = Cache::read($cacheName, 'permissions');
			if ($permissions === false) {
				$permissions = $this->Permissions->format($acos, $aros, $options);
				Cache::write($cacheName, $permissions, 'permissions');
			}
		} else {
			$permissions = array();
		}

		$this->set(compact('aros', 'permissions'));

		if ($this->request->is('ajax') && isset($query)) {
			$this->render('Croogo/Acl.acl_permissions_table');
		} else {
			$this->_setPermissionRoots();
		}
	}

	protected function _setPermissionRoots() {
		$roots = $this->Acos->getPermissionRoots();
		foreach ($roots as $id => $root) {
			Croogo::hookAdminTab(
				'Admin/Permissions/index',
				__d('croogo', $root->title),
				'Croogo/Croogo.blank',
				array(
					'linkOptions' => array(
						'data-alias' => $root->alias,
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
	public function toggle($acoId, $aroId) {
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
	public function upgrade() {
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
