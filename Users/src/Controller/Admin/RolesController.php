<?php

namespace Croogo\Users\Controller\Admin;

use Croogo\Croogo\Controller\CroogoAppController;

/**
 * Roles Controller
 *
 * @category Controller
 * @package  Croogo.Users.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class RolesController extends CroogoAppController {

/**
 * Admin index
 *
 * @return void
 * @access public
 */
	public function index() {
		$this->set('roles', $this->Roles->find('all'));
		$this->set('displayFields', $this->Roles->displayFields());
	}

/**
 * Admin add
 *
 * @return void
 * @access public
 */
	public function add() {
		$role = $this->Roles->newEntity();

		if ($this->request->is('post')) {
			$role = $this->Roles->patchEntity($role, $this->request->data());

			if ($this->Roles->save($role)) {
				$this->Flash->success(__d('croogo', 'The Role has been saved'));

				if ($this->request->data('apply') === null) {
					return $this->redirect(['action' => 'index']);
				} else {
					return $this->redirect(['action' => 'edit', $role->id]);
				}
			} else {
				$this->Flash->error(__d('croogo', 'The Role could not be saved. Please, try again.'));
			}
		}

		$this->set('role', $role);
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function edit($id = null) {
		$role = $this->Roles->get($id);

		if ($this->request->is('put')) {
			$this->Roles->patchEntity($role, $this->request->data());

			if ($this->Roles->save($role)) {
				$this->Flash->success(__d('croogo', 'The Role has been saved'));

				if ($this->request->data('apply') === null) {
					return $this->redirect(['action' => 'index']);
				} else {
					return $this->redirect(['action' => 'edit', $role->id]);
				}
			} else {
				$this->Flash->error(__d('croogo', 'The Role could not be saved. Please, try again.'));
			}
		}

		$this->set('role', $role);
	}

/**
 * Admin delete
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function delete($id = null) {
		$role = $this->Roles->get($id);

		if ($this->Roles->delete($role)) {
			$this->Flash->success(__d('croogo', 'Role deleted'));
		} else {
			$this->Flash->error(__d('croogo', 'Role cannot be deleted'));
		}
		return $this->redirect(['action' => 'index']);
	}

}
