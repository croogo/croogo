<?php
/**
 * Terms Controller
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
class TermsController extends AppController {
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
    public $uses = array('Term');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->vocabularyId = null;
        if (isset($this->params['named']['vocabulary'])) {
            $this->vocabularyId = $this->params['named']['vocabulary'];
        }
        $this->set('vocabulary', $this->vocabularyId);
    }

    public function admin_index($vocabularyId = null) {
        if (!$vocabularyId) {
            $this->redirect(array(
                'controller' => 'vocabularies',
                'action' => 'index',
            ));
        }
        $vocabulary = $this->Term->Vocabulary->findById($vocabularyId);
        if (!isset($vocabulary['Vocabulary']['id'])) {
            $this->Session->setFlash(__('Invalid Vocabulary ID.', true), 'default', array('class' => 'error'));
            $this->redirect(array(
                'controller' => 'vocabularies',
                'action' => 'index',
            ));
        }
        $this->set('title_for_layout', sprintf(__('Vocabulary: %s', true), $vocabulary['Vocabulary']['title']));

        $termsTree = $this->Term->Taxonomy->getTree($vocabulary['Vocabulary']['alias'], array(
            'key' => 'id',
            'value' => 'title',
        ));
        $terms = $this->Term->find('all', array(
            'conditions' => array(
                'Term.id' => array_keys($termsTree),
            ),
        ));
        $terms = Set::combine($terms, '{n}.Term.id', '{n}.Term');
        $this->set(compact('termsTree', 'vocabulary', 'terms'));
    }

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
        $this->set('title_for_layout', sprintf(__('%s: Add Term', true), $vocabulary['Vocabulary']['title']));

        if (!empty($this->data)) {
            $termId = $this->Term->saveAndGetId($this->data['Term']);
            if ($termId) {
                $termInVocabulary = $this->Term->Taxonomy->hasAny(array(
                    'Taxonomy.vocabulary_id' => $vocabularyId,
                    'Taxonomy.term_id' => $termId,
                ));
                if ($termInVocabulary) {
                    $this->Session->setFlash(__('Term with same slug already exists in the vocabulary.', true), 'default', array('class' => 'error'));
                } else {
                    $this->Term->Taxonomy->Behaviors->attach('Tree', array(
                        'scope' => array(
                            'Taxonomy.vocabulary_id' => $vocabularyId,
                        ),
                    ));
                    $taxonomy = array(
                        'parent_id' => $this->data['Taxonomy']['parent_id'],
                        'term_id' => $termId,
                        'vocabulary_id' => $vocabularyId,
                    );
                    if ($this->Term->Taxonomy->save($taxonomy)) {
                        $this->Session->setFlash(__('Term saved successfuly.', true), 'default', array('class' => 'success'));
                        $this->redirect(array(
                            'action' => 'index',
                            $vocabularyId,
                        ));
                    } else {
                        $this->Session->setFlash(__('Term could not be added to the vocabulary. Please try again.', true), 'default', array('class' => 'error'));
                    }
                }
            } else {
                $this->Session->setFlash(__('Term could not be saved. Please try again.', true), 'default', array('class' => 'error'));
            }
        }
        $parentTree = $this->Term->Taxonomy->getTree($vocabulary['Vocabulary']['alias'], array('taxonomyId' => true));
        $this->set(compact('vocabulary', 'parentTree'));
    }

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
        $this->set('title_for_layout', sprintf(__('%s: Edit Term', true), $vocabulary['Vocabulary']['title']));

        if (!empty($this->data)) {
            if ($term['Term']['slug'] != $this->data['Term']['slug']) {
                if ($this->Term->hasAny(array('Term.slug' => $this->data['Term']['slug']))) {
                    $termId = false;
                } else {
                    $termId = $this->Term->saveAndGetId($this->data['Term']);
                }
            } else {
                $this->Term->id = $term['Term']['id'];
                if (!$this->Term->save($this->data['Term'])) {
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
                    $this->Session->setFlash(__('Term with same slug already exists in the vocabulary.', true), 'default', array('class' => 'error'));
                } else {
                    $this->Term->Taxonomy->Behaviors->attach('Tree', array(
                        'scope' => array(
                            'Taxonomy.vocabulary_id' => $vocabularyId,
                        ),
                    ));
                    $taxonomy = array(
                        'id' => $taxonomy['Taxonomy']['id'],
                        'parent_id' => $this->data['Taxonomy']['parent_id'],
                        'term_id' => $termId,
                        'vocabulary_id' => $vocabularyId,
                    );
                    if ($this->Term->Taxonomy->save($taxonomy)) {
                        $this->Session->setFlash(__('Term saved successfuly.', true), 'default', array('class' => 'success'));
                        $this->redirect(array(
                            'action' => 'index',
                            $vocabularyId,
                        ));
                    } else {
                        $this->Session->setFlash(__('Term could not be added to the vocabulary. Please try again.', true), 'default', array('class' => 'error'));
                    }
                }
            } else {
                $this->Session->setFlash(__('Term could not be saved. Please try again.', true), 'default', array('class' => 'error'));
            }
        } else {
            $this->data['Taxonomy'] = $taxonomy['Taxonomy'];
            $this->data['Term'] = $term['Term'];
        }
        $parentTree = $this->Term->Taxonomy->getTree($vocabulary['Vocabulary']['alias'], array('taxonomyId' => true));
        $this->set(compact('vocabulary', 'parentTree', 'term', 'taxonomy'));
    }

    public function admin_delete($id = null, $vocabularyId = null) {
        if (!$id || !$vocabularyId) {
            $this->Session->setFlash(__('Invalid id for Term', true), 'default', array('class' => 'error'));
            $this->redirect(array(
                'action'=>'index',
                $vocabularyId,
            ));
        }
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
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
            $this->Session->setFlash(__('Term deleted', true), 'default', array('class' => 'success'));
        } else {
            $this->Session->setFlash(__('Term could not be deleted. Please, try again.', true), 'default', array('class' => 'error'));
        }
        $this->redirect(array(
            'action' => 'index',
            $vocabularyId,
        ));
    }

    public function admin_moveup($id = null, $vocabularyId = null, $step = 1) {
        if (!$id || !$vocabularyId) {
            $this->Session->setFlash(__('Invalid id for Term', true), 'default', array('class' => 'error'));
            $this->redirect(array(
                'action'=>'index',
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
        if( $this->Term->Taxonomy->moveup($taxonomyId, $step) ) {
            $this->Session->setFlash(__('Moved up successfully', true), 'default', array('class' => 'success'));
        } else {
            $this->Session->setFlash(__('Could not move up', true), 'default', array('class' => 'error'));
        }

        $this->redirect(array(
            'action' => 'index',
            $vocabularyId,
        ));
    }

    public function admin_movedown($id = null, $vocabularyId = null, $step = 1) {
        if (!$id || !$vocabularyId) {
            $this->Session->setFlash(__('Invalid id for Term', true), 'default', array('class' => 'error'));
            $this->redirect(array(
                'action'=>'index',
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

        if( $this->Term->Taxonomy->movedown($taxonomyId, $step) ) {
            $this->Session->setFlash(__('Moved down successfully', true), 'default', array('class' => 'success'));
        } else {
            $this->Session->setFlash(__('Could not move down', true), 'default', array('class' => 'error'));
        }

        $this->redirect(array(
            'action' => 'index',
            $vocabularyId,
        ));
    }

}
?>