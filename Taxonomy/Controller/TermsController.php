<?php

App::uses('TaxonomyAppController', 'Taxonomy.Controller');

/**
 * Terms Controller
 *
 * @category Taxonomy.Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TermsController extends TaxonomyAppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'Terms';
	protected $_redirectUrl = array(
		'plugin' => 'Taxonomy',
		'controller' => 'vocabularies',
		'action' => 'index',
		'admin' => true
	);
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array('Taxonomy.Term');

/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->vocabularyId = null;
		if (isset($this->request->params['named']['vocabulary'])) {
			$this->vocabularyId = $this->request->params['named']['vocabulary'];
		}
		$this->set('vocabulary', $this->vocabularyId);
	}

/**
 * Admin index
 *
 * @param integer $vocabularyId
 * @return void
 * @access public
 */
	public function admin_index($vocabularyId) {
		$this->__ensureVocabularyIdExists($vocabularyId);

		$vocabulary = $this->Term->Vocabulary->read(null, $vocabularyId);
		$defaultType = $this->__getDefaultType($vocabulary);
		$this->set('title_for_layout', __d('croogo', 'Vocabulary: %s', $vocabulary['Vocabulary']['title']));

		$terms = $this->Term->find('byVocabulary', array('vocabulary_id' => $vocabularyId));
		$this->set(compact('vocabulary', 'terms', 'defaultType'));

		if (isset($this->request->params['named']['links']) || isset($this->request->query['chooser'])) {
			$this->layout = 'admin_popup';
			$this->render('admin_chooser');
		}
	}

/**
 * Admin add
 *
 * @param integer $vocabularyId
 * @return void
 * @access public
 */
	public function admin_add($vocabularyId) {
		$this->__ensureVocabularyIdExists($vocabularyId);

		$vocabulary = $this->Term->Vocabulary->read(null, $vocabularyId);
		$this->set('title_for_layout', __d('croogo', '%s: Add Term', $vocabulary['Vocabulary']['title']));

		if ($this->request->is('post')) {
			if ($this->Term->add($this->request->data, $vocabularyId)) {
				$this->Session->setFlash(__d('croogo', 'Term saved successfuly.'), 'default', array('class' => 'success'));
				return $this->redirect(array(
					'action' => 'index',
					$vocabularyId,
				));
			} else {
				$this->Session->setFlash(__d('croogo', 'Term could not be added to the vocabulary. Please try again.'), 'default', array('class' => 'error'));
			}
		}
		$parentTree = $this->Term->Taxonomy->getTree($vocabulary['Vocabulary']['alias'], array('taxonomyId' => true));
		$this->set(compact('vocabulary', 'parentTree', 'vocabularyId'));
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @param integer $vocabularyId
 * @return void
 * @access public
 */
	public function admin_edit($id, $vocabularyId) {
		$this->__ensureVocabularyIdExists($vocabularyId);
		$this->__ensureTermExists($id);
		$this->__ensureTaxonomyExists($id, $vocabularyId);

		$vocabulary = $this->Term->Vocabulary->read(null, $vocabularyId);
		$term = $this->Term->find('first', array(
			'conditions' => array(
				'Term.id' => $id,
			),
		));
		$taxonomy = $this->Term->Taxonomy->find('first', array(
			'conditions' => array(
				'Taxonomy.term_id' => $id,
				'Taxonomy.vocabulary_id' => $vocabularyId,
			),
		));

		$this->set('title_for_layout', __d('croogo', '%s: Edit Term', $vocabulary['Vocabulary']['title']));

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Term->edit($this->request->data, $vocabularyId)) {
				$this->Session->setFlash(__d('croogo', 'Term saved successfuly.'), 'default', array('class' => 'success'));
				return $this->redirect(array(
					'action' => 'index',
					$vocabularyId,
				));
			} else {
				$this->Session->setFlash(__d('croogo', 'Term could not be added to the vocabulary. Please try again.'), 'default', array('class' => 'error'));
			}
		} else {
			$this->request->data['Taxonomy'] = $taxonomy['Taxonomy'];
			$this->request->data['Term'] = $term['Term'];
		}
		$parentTree = $this->Term->Taxonomy->getTree($vocabulary['Vocabulary']['alias'], array('taxonomyId' => true));
		$this->set(compact('vocabulary', 'parentTree', 'term', 'taxonomy', 'vocabularyId'));
	}

/**
 * Admin delete
 *
 * @param integer $id
 * @param integer $vocabularyId
 * @return void
 * @access public
 */
	public function admin_delete($id = null, $vocabularyId = null) {
		$redirectUrl = array('action' => 'index', $vocabularyId);
		$this->__ensureVocabularyIdExists($vocabularyId, $redirectUrl);
		$this->__ensureTermExists($id, $redirectUrl);
		$taxonomyId = $this->Term->Taxonomy->termInVocabulary($id, $vocabularyId);
		$this->__ensureVocabularyIdExists($vocabularyId, $redirectUrl);

		if ($this->Term->remove($id, $vocabularyId)) {
			$messageFlash = __d('croogo', 'Term deleted');
			$cssClass = array('class' => 'success');
		} else {
			$messageFlash = __d('croogo', 'Term could not be deleted. Please, try again.');
			$cssClass = array('class' => 'error');
		}

		$this->Session->setFlash($messageFlash, 'default', $cssClass);
		return $this->redirect($redirectUrl);
	}

/**
 * Admin moveup
 *
 * @param integer $id
 * @param integer $vocabularyId
 * @param integer $step
 * @return void
 * @access public
 */
	public function admin_moveup($id = null, $vocabularyId = null, $step = 1) {
		if (!$id || !$vocabularyId) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Term'), 'default', array('class' => 'error'));
			return $this->redirect(array(
				'action' => 'index',
				$vocabularyId,
			));
		}
		$taxonomyId = $this->Term->Taxonomy->termInVocabulary($id, $vocabularyId);
		if (!$taxonomyId) {
			return $this->redirect(array(
				'action' => 'index',
				$vocabularyId,
			));
		}
		$this->Term->Taxonomy->Behaviors->attach('Tree', array(
			'scope' => array(
				'Taxonomy.vocabulary_id' => $vocabularyId,
			),
		));
		if ($this->Term->Taxonomy->moveUp($taxonomyId, $step)) {
			$this->Session->setFlash(__d('croogo', 'Moved up successfully'), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'Could not move up'), 'default', array('class' => 'error'));
		}

		return $this->redirect(array(
			'action' => 'index',
			$vocabularyId,
		));
	}

/**
 * Admin movedown
 *
 * @param integer $id
 * @param integer $vocabularyId
 * @param integer $step
 * @return void
 * @access public
 */
	public function admin_movedown($id = null, $vocabularyId = null, $step = 1) {
		if (!$id || !$vocabularyId) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Term'), 'default', array('class' => 'error'));
			return $this->redirect(array(
				'action' => 'index',
				$vocabularyId,
			));
		}
		$taxonomyId = $this->Term->Taxonomy->termInVocabulary($id, $vocabularyId);
		if (!$taxonomyId) {
			return $this->redirect(array(
				'action' => 'index',
				$vocabularyId,
			));
		}
		$this->Term->Taxonomy->Behaviors->attach('Tree', array(
			'scope' => array(
				'Taxonomy.vocabulary_id' => $vocabularyId,
			),
		));

		if ($this->Term->Taxonomy->moveDown($taxonomyId, $step)) {
			$this->Session->setFlash(__d('croogo', 'Moved down successfully'), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'Could not move down'), 'default', array('class' => 'error'));
		}

		return $this->redirect(array(
			'action' => 'index',
			$vocabularyId,
		));
	}

	private function __getDefaultType($vocabulary) {
		if(isset($vocabulary['Type'][0])){
			$defaultType = $vocabulary['Type'][0];
		}
		if(isset($this->params->query['type_id'])){
			if(isset($vocabulary['Type'][$this->request->query['type_id']])){
				$defaultType = $vocabulary['Type'][$this->request->query['type_id']];
			}
		}
		return $defaultType;
	}

	private function __ensureTermExists($id) {
		if (!$this->Term->exists($id)) {
			$this->Session->setFlash(__d('croogo', 'Invalid Term ID.'), 'default', array('class' => 'error'));
			return $this->redirect($this->_redirectUrl);
		}
	}

	private function __ensureTaxonomyExists($id, $vocabularyId) {
		if (!$this->Term->Taxonomy->hasAny(array('term_id' => $id, 'vocabulary_id' => $vocabularyId))) {
			$this->Session->setFlash(__d('croogo', 'Invalid Taxonomy.'), 'default', array('class' => 'error'));
			return $this->redirect($this->_redirectUrl);
		}
	}

	private function __ensureVocabularyIdExists($vocabularyId) {
		if (!$vocabularyId) {
			return $this->redirect($this->_redirectUrl);
		}

		if (!$this->Term->Vocabulary->exists($vocabularyId)) {
			$this->Session->setFlash(__d('croogo', 'Invalid Vocabulary ID.'), 'default', array('class' => 'error'));
			return $this->redirect($this->_redirectUrl);
		}
	}
}
