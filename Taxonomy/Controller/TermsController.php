<?php

namespace Croogo\Taxonomy\Controller;
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
		'plugin' => 'taxonomy',
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
	public function admin_index($vocabularyId = null) {
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
		$this->__move('up', $id, $vocabularyId, $step);
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
		$this->__move('down', $id, $vocabularyId, $step);
	}

/**
 * __move
 *
 * @param string $direction either 'up' or 'down'
 * @param integer $id
 * @param integer $vocabularyId
 * @param integer $step
 * @return void
 * @access private
 */
	private function __move($direction, $id, $vocabularyId, $step) {
		$redirectUrl = array('action' => 'index', $vocabularyId);
		$this->__ensureVocabularyIdExists($vocabularyId, $redirectUrl);
		$this->__ensureTermExists($id, $redirectUrl);
		$taxonomyId = $this->Term->Taxonomy->termInVocabulary($id, $vocabularyId);
		$this->__ensureVocabularyIdExists($vocabularyId, $redirectUrl);

		$this->Term->setScopeForTaxonomy($vocabularyId);

		if ($this->Term->Taxonomy->{'move' . ucfirst($direction)}($taxonomyId, $step)) {
			$messageFlash = __d('croogo', 'Moved %s successfully', $direction);
			$cssClass = array('class' => 'success');
		} else {
			$messageFlash = __d('croogo', 'Could not move %s', $direction);
			$cssClass = array('class' => 'error');
		}
		$this->Session->setFlash($messageFlash, 'default', $cssClass);
		return $this->redirect($redirectUrl);
	}

/**
 * Get default type from Vocabulary
 */
	private function __getDefaultType($vocabulary) {
		$defaultType = null;
		if (isset($vocabulary['Type'][0])) {
			$defaultType = $vocabulary['Type'][0];
		}
		if (isset($this->params->query['type_id'])) {
			if (isset($vocabulary['Type'][$this->request->query['type_id']])) {
				$defaultType = $vocabulary['Type'][$this->request->query['type_id']];
			}
		}
		return $defaultType;
	}

/**
 * Check that Term exists or flash and redirect to $url when it is not found
 *
 * @param integer $id Term Id
 * @param string $url Redirect Url
 * @return bool True if Term exists
 */
	private function __ensureTermExists($id, $url = null) {
		$redirectUrl = is_null($url) ? $this->_redirectUrl : $url;
		if (!$this->Term->exists($id)) {
			$this->Session->setFlash(__d('croogo', 'Invalid Term ID.'), 'default', array('class' => 'error'));
			return $this->redirect($redirectUrl);
		}
	}

/**
 * Checks that Taxonomy exists or flash redirect to $url when it is not found
 *
 * @param integer $id Term Id
 * @param integer $vocabularyId Vocabulary Id
 * @param string $url Redirect Url
 * @return bool True if Term exists
 */
	private function __ensureTaxonomyExists($id, $vocabularyId, $url = null) {
		$redirectUrl = is_null($url) ? $this->_redirectUrl : $url;
		if (!$this->Term->Taxonomy->hasAny(array('term_id' => $id, 'vocabulary_id' => $vocabularyId))) {
			$this->Session->setFlash(__d('croogo', 'Invalid Taxonomy.'), 'default', array('class' => 'error'));
			return $this->redirect($redirectUrl);
		}
	}

/**
 * Checks that Vocabulary exists or flash redirect to $url when it is not found
 *
 * @param integer $vocabularyId Vocabulary Id
 * @param string $url Redirect Url
 * @return bool True if Term exists
 */
	private function __ensureVocabularyIdExists($vocabularyId, $url = null) {
		$redirectUrl = is_null($url) ? $this->_redirectUrl : $url;
		if (!$vocabularyId) {
			return $this->redirect($redirectUrl);
		}

		if (!$this->Term->Vocabulary->exists($vocabularyId)) {
			$this->Session->setFlash(__d('croogo', 'Invalid Vocabulary ID.'), 'default', array('class' => 'error'));
			return $this->redirect($redirectUrl);
		}
	}

}
