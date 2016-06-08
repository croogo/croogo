<?php

App::uses('TaxonomyAppController', 'Taxonomy.Controller');

/**
 * Vocabularies Controller
 *
 * @category Taxonomy.Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class VocabulariesController extends TaxonomyAppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'Vocabularies';

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array('Taxonomy.Vocabulary');

/**
 * Admin index
 *
 * @return void
 * @access public
 */
	public function admin_index() {
		$this->set('title_for_layout', __d('croogo', 'Vocabularies'));

		$this->Vocabulary->recursive = 0;
		$this->paginate['Vocabulary']['order'] = 'Vocabulary.weight ASC';
		$this->set('vocabularies', $this->paginate());
	}

/**
 * Admin add
 *
 * @return void
 * @access public
 */
	public function admin_add() {
		$this->set('title_for_layout', __d('croogo', 'Add Vocabulary'));

		if (!empty($this->request->data)) {
			$this->Vocabulary->create();
			if ($this->Vocabulary->saveAssociated($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'The Vocabulary has been saved'), 'flash', array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__d('croogo', 'The Vocabulary could not be saved. Please, try again.'), 'flash', array('class' => 'error'));
			}
		}

		$types = $this->Vocabulary->Type->pluginTypes();
		$this->set(compact('types'));
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_edit($id = null) {
		$this->set('title_for_layout', __d('croogo', 'Edit Vocabulary'));

		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__d('croogo', 'Invalid Vocabulary'), 'flash', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Vocabulary->saveAssociated($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'The Vocabulary has been saved'), 'flash', array('class' => 'success'));
				return $this->Croogo->redirect(array('action' => 'edit', $this->Vocabulary->id));
			} else {
				$this->Session->setFlash(__d('croogo', 'The Vocabulary could not be saved. Please, try again.'), 'flash', array('class' => 'error'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Vocabulary->read(null, $id);
		}

		$plugin = null;
		if (isset($this->request->data['Vocabulary']['plugin'])) {
			$plugin = $this->request->data['Vocabulary']['plugin'];
		}
		$types = $this->Vocabulary->Type->pluginTypes($plugin);
		$this->set(compact('types'));
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
			$this->Session->setFlash(__d('croogo', 'Invalid id for Vocabulary'), 'flash', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}
		if ($this->Vocabulary->delete($id)) {
			$this->Session->setFlash(__d('croogo', 'Vocabulary deleted'), 'flash', array('class' => 'success'));
			return $this->redirect(array('action' => 'index'));
		}
	}

/**
 * Admin moveup
 *
 * @param integer $id
 * @param integer $step
 * @return void
 * @access public
 */
	public function admin_moveup($id, $step = 1) {
		if ($this->Vocabulary->moveUp($id, $step)) {
			$this->Session->setFlash(__d('croogo', 'Moved up successfully'), 'flash', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'Could not move up'), 'flash', array('class' => 'error'));
		}

		return $this->redirect(array('action' => 'index'));
	}

/**
 * Admin moveup
 *
 * @param integer $id
 * @param integer $step
 * @return void
 * @access public
 */
	public function admin_movedown($id, $step = 1) {
		if ($this->Vocabulary->moveDown($id, $step)) {
			$this->Session->setFlash(__d('croogo', 'Moved down successfully'), 'flash', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'Could not move down'), 'flash', array('class' => 'error'));
		}

		return $this->redirect(array('action' => 'index'));
	}

}
