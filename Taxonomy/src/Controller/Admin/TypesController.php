<?php

namespace Croogo\Taxonomy\Controller\Admin;

use Cake\Event\Event;
use Croogo\Taxonomy\Model\Table\TypesTable;

/**
 * Types Controller
 *
 * @property TypesTable Types
 * @category Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TypesController extends AppController {

/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	public function beforeFilter(Event $event) {
		parent::beforeFilter($event);

		if ($this->action == 'edit') {
			$this->Security->disabledFields = array('alias');
		}
	}

/**
 * Admin index
 *
 * @return void
 * @access public
 */
	public function index() {
		$this->paginate = [
			'order' => [
				'title' => 'ASC'
			],
		];

		$findQuery = $this->Types->find('all');

		$this->set('types', $this->paginate($findQuery));
		$this->set('displayFields', $this->Types->displayFields());
	}

/**
 * Admin add
 *
 * @return void
 * @access public
 */
	public function add() {
		$this->set('title_for_layout', __d('croogo', 'Add Type'));

		$type = $this->Types->newEntity();

		if (!empty($this->request->data)) {
			$type = $this->Types->patchEntity($type, $this->request->data);
			$type = $this->Types->save($type);
			if ($type) {
				$this->Flash->success(__d('croogo', 'The Type has been saved'));
				$this->Croogo->redirect(array('action' => 'edit', $type->id));
			} else {
				$this->Flash->error(__d('croogo', 'The Type could not be saved. Please, try again.'));
			}
		}

		$this->set(compact('type'));

		$vocabularies = $this->Types->Vocabularies->find('list');
		$this->set(compact('vocabularies'));
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Flash->error(__d('croogo', 'Invalid Type'));

			return $this->redirect(array('action' => 'index'));
		}

		$type = $this->Types->get($id);

		if (!empty($this->request->data)) {
			$type = $this->Types->patchEntity($type, $this->request->data);

			$type = $this->Types->save($type);
			if ($this->Types->save($type)) {
				$this->Flash->success(__d('croogo', 'The Type has been saved'));

				$this->Croogo->redirect(array('action' => 'edit', $type->id));
			} else {
				$this->Flash->error(__d('croogo', 'The Type could not be saved. Please, try again.'));
			}
		}

		$this->set('type', $type);

		$vocabularies = $this->Types->Vocabularies->find('list');
		$this->set(compact('vocabularies'));
	}

/**
 * Admin delete
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function delete($id = null) {
		$type = $this->Types->get($id);

		if (!$id) {
			$this->Flash->error(__d('croogo', 'Invalid id for Type'));

			return $this->redirect(array('action' => 'index'));
		}
		if ($this->Types->delete($type)) {
			$this->Flash->success(__d('croogo', 'Type deleted'));

			return $this->redirect(array('action' => 'index'));
		}
	}

}
