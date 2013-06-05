<?php

App::uses('AppController', 'Controller');

/**
 * Types Controller
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
class TypesController extends AppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'Types';

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array('Taxonomy.Type');

/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	public function beforeFilter() {
		parent::beforeFilter();
		if ($this->action == 'admin_edit') {
			$this->Security->disabledFields = array('alias');
		}
	}

/**
 * Admin index
 *
 * @return void
 * @access public
 */
	public function admin_index() {
		$this->set('title_for_layout', __d('croogo', 'Type'));

		$this->Type->recursive = 0;
		$this->paginate['Type']['order'] = 'Type.title ASC';
		$this->set('types', $this->paginate());
		$this->set('displayFields', $this->Type->displayFields());
	}

/**
 * Admin add
 *
 * @return void
 * @access public
 */
	public function admin_add() {
		$this->set('title_for_layout', __d('croogo', 'Add Type'));

		if (!empty($this->request->data)) {
			$this->Type->create();
			if ($this->Type->save($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'The Type has been saved'), 'default', array('class' => 'success'));
				$this->Croogo->redirect(array('action' => 'edit', $this->Type->id));
			} else {
				$this->Session->setFlash(__d('croogo', 'The Type could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		}

		$vocabularies = $this->Type->Vocabulary->find('list');
		$this->set(compact('vocabularies'));
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_edit($id = null) {
		$this->set('title_for_layout', __d('croogo', 'Edit Type'));

		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__d('croogo', 'Invalid Type'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Type->save($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'The Type has been saved'), 'default', array('class' => 'success'));
				$this->Croogo->redirect(array('action' => 'edit', $this->Type->id));
			} else {
				$this->Session->setFlash(__d('croogo', 'The Type could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Type->read(null, $id);
		}

		$vocabularies = $this->Type->Vocabulary->find('list');
		$this->set(compact('vocabularies'));
	}

/**
 * Admin delete
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Type'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Type->delete($id)) {
			$this->Session->setFlash(__d('croogo', 'Type deleted'), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		}
	}
}
