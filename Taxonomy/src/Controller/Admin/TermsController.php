<?php

namespace Croogo\Taxonomy\Controller\Admin;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\Network\Response;
use Croogo\Taxonomy\Controller\TaxonomyAppController;
use Croogo\Taxonomy\Model\Table\TermsTable;

/**
 * Terms Controller
 *
 * @property TermsTable Terms
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
		'prefix' => 'admin',
		'plugin' => 'Croogo/Taxonomy',
		'controller' => 'Vocabularies',
		'action' => 'index',
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
	public function beforeFilter(Event $event) {
		parent::beforeFilter($event);
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
 * @access public
 */
	public function index($vocabularyId = null) {
		$response = $this->__ensureVocabularyIdExists($vocabularyId);
		if ($response instanceof Response) {
			return $response;
		}

		$vocabulary = $this->Terms->Vocabularies->get($vocabularyId);
		$defaultType = $this->__getDefaultType($vocabulary);

		$terms = $this->Terms->find('byVocabulary', array('vocabulary_id' => $vocabularyId));
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
 * @access public
 */
	public function add($vocabularyId) {
		$response = $this->__ensureVocabularyIdExists($vocabularyId);
		if ($response instanceof Response) {
			return $response;
		}

		$vocabulary = $this->Terms->Vocabularies->get($vocabularyId);

		$term = $this->Terms->newEntity();
		$this->set('term', $term);

		if ($this->request->is('post')) {
			$term = $this->Terms->patchEntity($term, $this->request->data);

			if ($this->Terms->add($term, $vocabularyId)) {
				$this->Flash->success(__d('croogo', 'Term saved successfuly.'));
				return $this->redirect(array(
					'action' => 'index',
					$vocabularyId,
				));
			} else {
				$this->Flash->error(__d('croogo', 'Term could not be added to the vocabulary. Please try again.'));
			}
		}
		$parentTree = $this->Terms->Taxonomies->getTree($vocabulary->alias, array('taxonomyId' => true));
		$this->set(compact('vocabulary', 'parentTree', 'vocabularyId'));
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @param integer $vocabularyId
 * @access public
 */
	public function edit($id, $vocabularyId) {
		$response = $this->__ensureVocabularyIdExists($vocabularyId);
		if ($response instanceof Response) {
			return $response;
		}
		$response = $this->__ensureTermExists($id);
		if ($response instanceof Response) {
			return $response;
		}
		$response = $this->__ensureTaxonomyExists($id, $vocabularyId);
		if ($response instanceof Response) {
			return $response;
		}

		$vocabulary = $this->Terms->Vocabularies->get($vocabularyId);
		$term = $this->Terms->get($id, [
			'contain' => [
				'Taxonomies'
			]
		]);
		$taxonomy = $this->Terms->Taxonomies->find()->where([
				'Taxonomies.term_id' => $id,
				'Taxonomies.vocabulary_id' => $vocabularyId,
		])->first();

		$this->set('title_for_layout', __d('croogo', '%s: Edit Term', $vocabulary->title));

		if ($this->request->is('post') || $this->request->is('put')) {
			$term = $this->Terms->patchEntity($term, $this->request->data);
			if ($this->Terms->edit($term, $vocabularyId)) {
				$this->Flash->success(__d('croogo', 'Term saved successfuly.'));
				return $this->redirect(array(
					'action' => 'index',
					$vocabularyId,
				));
			} else {
				$this->Flash->error(__d('croogo', 'Term could not be added to the vocabulary. Please try again.'));
			}
		}
		$parentTree = $this->Terms->Taxonomies->getTree($vocabulary->alias, array('taxonomyId' => true));
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
	public function delete($id = null, $vocabularyId = null) {
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
	public function moveup($id = null, $vocabularyId = null, $step = 1) {
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
	public function movedown($id = null, $vocabularyId = null, $step = 1) {
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
		try {
			$this->Terms->get($id);
		} catch (RecordNotFoundException $exception) {
			$this->Flash->error(__d('croogo', 'Invalid Term ID.'));
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
		$count = $this->Terms->Taxonomies->find()->where(['term_id' => $id, 'vocabulary_id' => $vocabularyId])->count();
		if (!$count) {
			$this->Flash->error(__d('croogo', 'Invalid Taxonomy.'));
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

		try {
			$this->Terms->Vocabularies->get($vocabularyId);
		} catch (RecordNotFoundException $recordNotFoundException) {
			$this->Flash->error(__d('croogo', 'Invalid Vocabulary ID.'));

			return $this->redirect($redirectUrl);
		}
	}

}
