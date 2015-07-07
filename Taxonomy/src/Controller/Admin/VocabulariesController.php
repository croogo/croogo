<?php

namespace Croogo\Taxonomy\Controller\Admin;

use Croogo\Taxonomy\Controller\TaxonomyAppController;
use Croogo\Taxonomy\Model\Table\VocabulariesTable;

/**
 * Vocabularies Controller
 *
 * @property VocabulariesTable Vocabularies
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
	public function index() {
		$this->set('title_for_layout', __d('croogo', 'Vocabularies'));

		$this->paginate = [
			'order' => [
				'weight' => 'ASC'
			]
		];

		$findQuery = $this->Vocabularies->find('all');

		$this->set('vocabularies', $this->paginate($findQuery));
	}

/**
 * Admin add
 *
 * @return void
 * @access public
 */
	public function add() {
		$vocabulary = $this->Vocabularies->newEntity();

		if (!empty($this->request->data)) {
			$vocabulary = $this->Vocabularies->patchEntity($vocabulary, $this->request->data);

			$vocabulary = $this->Vocabularies->save($vocabulary);
			if ($vocabulary) {
				$this->Flash->success(__d('croogo', 'The Vocabulary has been saved'));

				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__d('croogo', 'The Vocabulary could not be saved. Please, try again.'));
			}
		}

		$this->set('vocabulary', $vocabulary);

		$types = $this->Vocabularies->Types->pluginTypes();
		$this->set(compact('types'));
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
			$this->Flash->error(__d('croogo', 'Invalid Vocabulary'));

			return $this->redirect(array('action' => 'index'));
		}

		$vocabulary = $this->Vocabularies->get($id);

		if (!empty($this->request->data)) {
			$vocabulary = $this->Vocabularies->patchEntity($vocabulary, $this->request->data);

			$vocabulary = $this->Vocabularies->save($vocabulary);
			if ($vocabulary) {
				$this->Flash->success(__d('croogo', 'The Vocabulary has been saved'));

				return $this->Croogo->redirect(array('action' => 'edit', $vocabulary->id));
			} else {
				$this->Flash->error(__d('croogo', 'The Vocabulary could not be saved. Please, try again.'));
			}
		}

		$this->set('vocabulary', $vocabulary);

		$plugin = null;
		if ($this->request->data('plugin')) {
			$plugin = $this->request->data('plugin');
		}
		$types = $this->Vocabularies->Types->pluginTypes($plugin);
		$this->set(compact('types'));
	}

/**
 * Admin delete
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function delete($id = null) {
		$vocabulary = $this->Vocabularies->get($id);

		if (!$id) {
			$this->Flash->error(__d('croogo', 'Invalid id for Vocabulary'));

			return $this->redirect(array('action' => 'index'));
		}
		if ($this->Vocabularies->delete($vocabulary)) {
			$this->Flash->success(__d('croogo', 'Vocabulary deleted'));

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
	public function moveup($id, $step = 1) {
		$vocabulary = $this->Vocabularies->get($id);
		$vocabulary->weight = $vocabulary->weight - $step;

		if ($this->Vocabularies->save($vocabulary)) {
			$this->Flash->success(__d('croogo', 'Moved up successfully'));
		} else {
			$this->Flash->error(__d('croogo', 'Could not move up'), 'default', array('class' => 'error'));
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
	public function movedown($id, $step = 1) {
		$vocabulary = $this->Vocabularies->get($id);
		$vocabulary->weight = $vocabulary->weight + $step;

		if ($this->Vocabularies->save($vocabulary)) {
			$this->Flash->success(__d('croogo', 'Moved down successfully'));
		} else {
			$this->Flash->error(__d('croogo', 'Could not move down'));
		}

		return $this->redirect(array('action' => 'index'));
	}

}
