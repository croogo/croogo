<?php

namespace Croogo\Acl\Controller\Admin;

use Acl\AclExtras;
use Acl\Controller\AclAppController;
use Cake\Event\Event;
use Croogo\Croogo\Controller\CroogoAppController;

/**
 * AclActions Controller
 *
 * @category Controller
 * @package  Croogo.Acl
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ActionsController extends CroogoAppController {

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter(Event $event) {
		parent::beforeFilter($event);
		if ($this->request->param('action') == 'generate' && $this->request->param('prefix') == 'admin') {
			$this->Security->csrfCheck = false;
		}
	}

/**
 * admin_index
 */
	public function index($id = null) {
		$this->set('title_for_layout', __d('croogo', 'Actions'));

		if ($id == null) {
			$root = $this->Acl->Aco->node('controllers');
			$root = $root[0];
		} else {
			$root = $this->Acl->Aco->read(null, $id);
		}

		$acos = $this->AclAco->getChildren($root['Aco']['id']);
		$this->set(compact('acos'));
	}

/**
 * admin_add
 */
	public function add() {
		$this->set('title_for_layout', __d('croogo', 'Add Action'));

		if (!empty($this->request->data)) {
			$this->Acl->Aco->create();

			// if parent_id is null, assign 'controllers' as parent
			if ($this->request->data['Aco']['parent_id'] == null) {
				$this->request->data['Aco']['parent_id'] = 1;
				$acoType = 'Controller';
			} else {
				$acoType = 'Action';
			}

			if ($this->Acl->Aco->save($this->request->data['Aco'])) {
				$this->Session->setFlash(sprintf(__d('croogo', 'The %s has been saved'), $acoType), 'default', array('class' => 'success'));
				return $this->Croogo->redirect(array('action' => 'edit', $this->Acl->Aco->id));
			} else {
				$this->Session->setFlash(sprintf(__d('croogo', 'The %s could not be saved. Please, try again.'), $acoType), 'default', array('class' => 'error'));
			}
		}

		$acos = $this->Acl->Aco->generateTreeList(null, '{n}.Aco.id', '{n}.Aco.alias');
		$this->set(compact('acos'));
	}

/**
 * admin_edit
 *
 * @param integer $id
 */
	public function edit($id = null) {
		$this->set('title_for_layout', __d('croogo', 'Edit Action'));

		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__d('croogo', 'Invalid Action'), 'default', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Acl->Aco->save($this->request->data['Aco'])) {
				$this->Session->setFlash(__d('croogo', 'The Action has been saved'), 'default', array('class' => 'success'));
				return $this->Croogo->redirect(array('action' => 'edit', $this->Acl->Aco->id));
			} else {
				$this->Session->setFlash(__d('croogo', 'The Action could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Acl->Aco->read(null, $id);
		}

		$acos = $this->Acl->Aco->generateTreeList(null, '{n}.Aco.id', '{n}.Aco.alias');
		$this->set(compact('acos'));
	}

/**
 * admin_delete
 *
 * @param integer $id
 */
	public function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Action'), 'default', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}
		if ($this->Acl->Aco->delete($id)) {
			$this->Session->setFlash(__d('croogo', 'Action deleted'), 'default', array('class' => 'success'));
			return $this->redirect(array('action' => 'index'));
		}
	}

/**
 * admin_move
 *
 * @param integer $id
 * @param string $direction
 * @param string $step
 */
	public function move($id, $direction = 'up', $step = '1') {
		if (!$id) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Action'), 'default', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}
		if ($direction == 'up') {
			if ($this->Acl->Aco->moveUp($id)) {
				$this->Session->setFlash(__d('croogo', 'Action moved up'), 'default', array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			}
		} else {
			if ($this->Acl->Aco->moveDown($id)) {
				$this->Session->setFlash(__d('croogo', 'Action moved down'), 'default', array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			}
		}
	}

/**
 * admin_generate
 */
	public function generate() {
		$AclExtras = new AclExtras();
		$AclExtras->startup($this);
		if (isset($this->request->query['sync'])) {
			$AclExtras->acoSync();
		} else {
			$AclExtras->acoUpdate();
		}

		if (isset($this->request->query['permissions'])) {
			return $this->redirect(array('plugin' => 'Croogo/Acl', 'controller' => 'Permissions', 'action' => 'index'));
		} else {
			return $this->redirect(array('action' => 'index'));
		}
	}

}
