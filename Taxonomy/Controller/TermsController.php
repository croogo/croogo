<?php

App::uses('TaxonomyAppController', 'Taxonomy.Controller');

/**
 * Terms Controller
 *
 * PHP version 5
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
	public function admin_index($vocabularyId = null) {
		if (!$vocabularyId) {
			$this->redirect(array(
				'controller' => 'vocabularies',
				'action' => 'index',
			));
		}
		$vocabulary = $this->Term->Vocabulary->findById($vocabularyId);
		if (!isset($vocabulary['Vocabulary']['id'])) {
			$this->Session->setFlash(__d('croogo', 'Invalid Vocabulary ID.'), 'default', array('class' => 'error'));
			$this->redirect(array(
				'controller' => 'vocabularies',
				'action' => 'index',
			));
		}
		$this->set('title_for_layout', __d('croogo', 'Vocabulary: %s', $vocabulary['Vocabulary']['title']));

		$termsTree = $this->Term->Taxonomy->getTree($vocabulary['Vocabulary']['alias'], array(
			'key' => 'id',
			'value' => 'title',
		));
		$terms = $this->Term->find('all', array(
			'conditions' => array(
				'Term.id' => array_keys($termsTree),
			),
		));
		$terms = Hash::combine($terms, '{n}.Term.id', '{n}.Term');
		$this->set(compact('termsTree', 'vocabulary', 'terms'));
	}

/**
 * Admin add
 *
 * @param integer $vocabularyId
 * @return void
 * @access public
 */
	public function admin_add($vocabularyId = null) {
		if (!$vocabularyId) {
			$this->redirect(array(
				'controller' => 'vocabularies',
				'action' => 'index',
			));
		}
		$vocabulary = $this->Term->Vocabulary->find('first', array(
			'conditions' => array(
				'Vocabulary.id' => $vocabularyId,
			),
		));
		if (!isset($vocabulary['Vocabulary']['id'])) {
			$this->redirect(array(
				'controller' => 'vocabularies',
				'action' => 'index',
			));
		}
		$this->set('title_for_layout', __d('croogo', '%s: Add Term', $vocabulary['Vocabulary']['title']));

		if (!empty($this->request->data)) {
			$termId = $this->Term->saveAndGetId($this->request->data['Term']);
			if ($termId) {
				$termInVocabulary = $this->Term->Taxonomy->hasAny(array(
					'Taxonomy.vocabulary_id' => $vocabularyId,
					'Taxonomy.term_id' => $termId,
				));
				if ($termInVocabulary) {
					$this->Session->setFlash(__d('croogo', 'Term with same slug already exists in the vocabulary.'), 'default', array('class' => 'error'));
				} else {
					$this->Term->Taxonomy->Behaviors->attach('Tree', array(
						'scope' => array(
							'Taxonomy.vocabulary_id' => $vocabularyId,
						),
					));
					$taxonomy = array(
						'parent_id' => $this->request->data['Taxonomy']['parent_id'],
						'term_id' => $termId,
						'vocabulary_id' => $vocabularyId,
					);
					if ($this->Term->Taxonomy->save($taxonomy)) {
						$this->Session->setFlash(__d('croogo', 'Term saved successfuly.'), 'default', array('class' => 'success'));
						$this->redirect(array(
							'action' => 'index',
							$vocabularyId,
						));
					} else {
						$this->Session->setFlash(__d('croogo', 'Term could not be added to the vocabulary. Please try again.'), 'default', array('class' => 'error'));
					}
				}
			} else {
				$this->Session->setFlash(__d('croogo', 'Term could not be saved. Please try again.'), 'default', array('class' => 'error'));
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
	public function admin_edit($id = null, $vocabularyId = null) {
		if (!$vocabularyId) {
			$this->redirect(array(
				'controller' => 'vocabularies',
				'action' => 'index',
			));
		}
		$vocabulary = $this->Term->Vocabulary->find('first', array(
			'conditions' => array(
				'Vocabulary.id' => $vocabularyId,
			),
		));
		if (!isset($vocabulary['Vocabulary']['id'])) {
			$this->redirect(array(
				'controller' => 'vocabularies',
				'action' => 'index',
			));
		}
		$term = $this->Term->find('first', array(
			'conditions' => array(
				'Term.id' => $id,
			),
		));
		if (!isset($term['Term']['id'])) {
			$this->redirect(array(
				'controller' => 'vocabularies',
				'action' => 'index',
			));
		}
		$taxonomy = $this->Term->Taxonomy->find('first', array(
			'conditions' => array(
				'Taxonomy.term_id' => $id,
				'Taxonomy.vocabulary_id' => $vocabularyId,
			),
		));
		if (!isset($taxonomy['Taxonomy']['id'])) {
			$this->redirect(array(
				'controller' => 'vocabularies',
				'action' => 'index',
			));
		}
		$this->set('title_for_layout', __d('croogo', '%s: Edit Term', $vocabulary['Vocabulary']['title']));

		if (!empty($this->request->data)) {
			if ($term['Term']['slug'] != $this->request->data['Term']['slug']) {
				if ($this->Term->hasAny(array('Term.slug' => $this->request->data['Term']['slug']))) {
					$termId = false;
				} else {
					$termId = $this->Term->saveAndGetId($this->request->data['Term']);
				}
			} else {
				$this->Term->id = $term['Term']['id'];
				if (!$this->Term->save($this->request->data['Term'])) {
					$termId = false;
				} else {
					$termId = $term['Term']['id'];
				}
			}

			if ($termId) {
				$termInVocabulary = $this->Term->Taxonomy->hasAny(array(
					'Taxonomy.id !=' => $taxonomy['Taxonomy']['id'],
					'Taxonomy.vocabulary_id' => $vocabularyId,
					'Taxonomy.term_id' => $termId,
				));
				if ($termInVocabulary) {
					$this->Session->setFlash(__d('croogo', 'Term with same slug already exists in the vocabulary.'), 'default', array('class' => 'error'));
				} else {
					$this->Term->Taxonomy->Behaviors->attach('Tree', array(
						'scope' => array(
							'Taxonomy.vocabulary_id' => $vocabularyId,
						),
					));
					$taxonomy = array(
						'id' => $taxonomy['Taxonomy']['id'],
						'parent_id' => $this->request->data['Taxonomy']['parent_id'],
						'term_id' => $termId,
						'vocabulary_id' => $vocabularyId,
					);
					if ($this->Term->Taxonomy->save($taxonomy)) {
						$this->Session->setFlash(__d('croogo', 'Term saved successfuly.'), 'default', array('class' => 'success'));
						$this->redirect(array(
							'action' => 'index',
							$vocabularyId,
						));
					} else {
						$this->Session->setFlash(__d('croogo', 'Term could not be added to the vocabulary. Please try again.'), 'default', array('class' => 'error'));
					}
				}
			} else {
				$this->Session->setFlash(__d('croogo', 'Term could not be saved. Please try again.'), 'default', array('class' => 'error'));
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
		if (!$id || !$vocabularyId) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Term'), 'default', array('class' => 'error'));
			$this->redirect(array(
				'action' => 'index',
				$vocabularyId,
			));
		}
		$taxonomyId = $this->Term->Taxonomy->termInVocabulary($id, $vocabularyId);
		if (!$taxonomyId) {
			$this->redirect(array(
				'action' => 'index',
				$vocabularyId,
			));
		}
		$this->Term->Taxonomy->Behaviors->attach('Tree', array(
			'scope' => array(
				'Taxonomy.vocabulary_id' => $vocabularyId,
			),
		));
		if ($this->Term->Taxonomy->delete($taxonomyId)) {
			$this->Session->setFlash(__d('croogo', 'Term deleted'), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'Term could not be deleted. Please, try again.'), 'default', array('class' => 'error'));
		}
		$this->redirect(array(
			'action' => 'index',
			$vocabularyId,
		));
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
			$this->redirect(array(
				'action' => 'index',
				$vocabularyId,
			));
		}
		$taxonomyId = $this->Term->Taxonomy->termInVocabulary($id, $vocabularyId);
		if (!$taxonomyId) {
			$this->redirect(array(
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

		$this->redirect(array(
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
			$this->redirect(array(
				'action' => 'index',
				$vocabularyId,
			));
		}
		$taxonomyId = $this->Term->Taxonomy->termInVocabulary($id, $vocabularyId);
		if (!$taxonomyId) {
			$this->redirect(array(
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

		$this->redirect(array(
			'action' => 'index',
			$vocabularyId,
		));
	}

}
