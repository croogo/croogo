<?php

namespace Croogo\Acl\Controller\Admin;

use Cake\Cache\Cache;
use Cake\Event\Event;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\ORM\TableRegistry;
use Croogo\Core\Controller\Admin\Controller;
use Croogo\Core\Croogo;

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
class PermissionsController extends Controller {

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
			$cacheName = 'permissions_aco_' . $root->id;
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
				'Croogo/Core.blank',
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
		if (!$this->request->is('ajax')) {
			return $this->redirect(array('action' => 'index'));
		}

		// see if acoId and aroId combination exists
		$aro = $this->Aros->get($aroId)->toArray();
		$path = $this->Acos->find('path', ['for' => $acoId]);
		$path = join('/', collection($path)->extract('alias')->toArray());

		$permitted = !$this->Permissions->check($aro, $path);
		$success = $this->Permissions->allow($aro, $path, '*', $permitted ? 1 : -1);
		if ($success) {
			$aco = $this->Acos->get($acoId);
			$cacheName = 'permissions_aco_' . $aco->parent_id;
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
			$this->Session->setFlash(__d('croogo', 'ACL database has been upgraded successfully'), 'flash', array('class' => 'success'));
		} else {
			$this->Session->setFlash(join('<br>', $result), 'flash', array('class' => 'error'));
		}
		return $this->redirect($this->referer());
	}

}
