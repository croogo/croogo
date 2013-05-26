<?php

App::uses('AclAppController', 'Acl.Controller');

/**
 * AclActions Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Croogo.Acl
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclActionsController extends AclAppController {

/**
 * name
 *
 * @var string
 */
	public $name = 'AclActions';

/**
 * uses
 *
 * @var array
 */
	public $uses = array('Acl.AclAco');

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		if ($this->action == 'admin_generate') {
			$this->Security->csrfCheck = false;
		}
	}

/**
 * admin_index
 */
	public function admin_index($id = null) {
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
	public function admin_add() {
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
				$this->redirect(array('action' => 'index'));
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
	public function admin_edit($id = null) {
		$this->set('title_for_layout', __d('croogo', 'Edit Action'));

		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__d('croogo', 'Invalid Action'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Acl->Aco->save($this->request->data['Aco'])) {
				$this->Session->setFlash(__d('croogo', 'The Action has been saved'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
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
	public function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Action'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Acl->Aco->delete($id)) {
			$this->Session->setFlash(__d('croogo', 'Action deleted'), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		}
	}

/**
 * admin_move
 *
 * @param integer $id
 * @param string $direction
 * @param string $step
 */
	public function admin_move($id, $direction = 'up', $step = '1') {
		if (!$id) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Action'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if ($direction == 'up') {
			if ($this->Acl->Aco->moveUp($id)) {
				$this->Session->setFlash(__d('croogo', 'Action moved up'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
			}
		} else {
			if ($this->Acl->Aco->moveDown($id)) {
				$this->Session->setFlash(__d('croogo', 'Action moved down'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
			}
		}
	}

/**
 * admin_generate
 */
	public function admin_generate() {
		App::uses('AclExtras', 'Acl.Lib');
		$AclExtras = new AclExtras();
		$AclExtras->startup($this);
		if (isset($this->request->named['sync'])) {
			$result = $AclExtras->aco_sync();
		} else {
			$result = $AclExtras->aco_update();
		}
		$output = $AclExtras->output;
		$output += $AclExtras->errors;
		if ($result) {
			$class = 'success';
			$output[] = __d('croogo', 'Created %d new permissions', $AclExtras->created);
		} else {
			$class = 'error';
		}

		$this->Session->setFlash(join('<br>', $output), 'default', array('class' => $class));

		if (isset($this->params['named']['permissions'])) {
			$this->redirect(array('plugin' => 'acl', 'controller' => 'acl_permissions', 'action' => 'index'));
		} else {
			$this->redirect(array('action' => 'index'));
		}
	}

}
