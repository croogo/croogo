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

    public function admin_index() {
        $this->set('title_for_layout', __('Terms', true));

        if ($this->vocabularyId != null) {
            $vocabulary = $this->Term->Vocabulary->findById($this->vocabularyId);
            $this->set('title_for_layout', sprintf(__('Terms: %s', true), $vocabulary['Vocabulary']['title']));
            $this->paginate['Term']['conditions']['vocabulary_id'] = $this->vocabularyId;
        }

        $treeConditions = array(
            'Term.vocabulary_id' => $this->vocabularyId,
        );
        $termsTree = $this->Term->generatetreelist($treeConditions);
        $this->set(compact('termsTree'));
    }

    public function admin_add() {
        $this->set('title_for_layout', __('Add Term', true));

        if (!empty($this->data)) {
            $this->Term->create();
            if ($this->Term->save($this->data)) {
                $this->Session->setFlash(__('The Term has been saved', true));
                $this->redirect(array('action'=>'index', 'vocabulary' => $this->vocabularyId));
            } else {
                $this->Session->setFlash(__('The Term could not be saved. Please, try again.', true));
            }
        }
        $vocabularies = $this->Term->Vocabulary->find('list');
        $findTerm = array();
        if ($this->vocabularyId != null) {
            $findTerm['conditions']['vocabulary_id'] = $this->vocabularyId;
        }
        $terms = $this->Term->generatetreelist(array('Term.vocabulary_id' => $this->vocabularyId), '{n}.Term.id', '{n}.Term.title'); //$this->Term->find('list', $findTerm);
        $this->set(compact('vocabularies', 'terms'));
    }

    public function admin_edit($id = null) {
        $this->set('title_for_layout', __('Edit Term', true));

        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Term', true));
            $this->redirect(array('action'=>'index', 'vocabulary' => $this->vocabularyId));
        }
        if (!empty($this->data)) {
            if ($this->Term->save($this->data)) {
                $this->Session->setFlash(__('The Term has been saved', true));
                $this->redirect(array('action'=>'index', 'vocabulary' => $this->vocabularyId));
            } else {
                $this->Session->setFlash(__('The Term could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Term->read(null, $id);
        }
        $vocabularies = $this->Term->Vocabulary->find('list');
        $terms = $this->Term->generatetreelist(array('Term.vocabulary_id' => $this->vocabularyId), '{n}.Term.id', '{n}.Term.title'); //$this->Term->find('list');
        $this->set(compact('vocabularies', 'terms'));
    }

    public function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Term', true));
            $this->redirect(array('action'=>'index', 'vocabulary' => $this->vocabularyId));
        }
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
        }
        if ($this->Term->delete($id)) {
            $this->Session->setFlash(__('Term deleted', true));
            $this->redirect(array('action'=>'index', 'vocabulary' => $this->vocabularyId));
        }
    }

    public function admin_moveup($id, $step = 1) {
        if( $this->Term->moveup($id, $step) ) {
            $this->Session->setFlash(__('Moved up successfully', true));
        } else {
            $this->Session->setFlash(__('Could not move up', true));
        }

        $this->redirect(array('action' => 'index', 'vocabulary' => $this->vocabularyId));
    }

    public function admin_movedown($id, $step = 1) {
        if( $this->Term->movedown($id, $step) ) {
            $this->Session->setFlash(__('Moved down successfully', true));
        } else {
            $this->Session->setFlash(__('Could not move down', true));
        }

        $this->redirect(array('action' => 'index', 'vocabulary' => $this->vocabularyId));
    }

    public function admin_process() {
        $action = $this->data['Term']['action'];
        $ids = array();
        foreach ($this->data['Term'] AS $id => $value) {
            if ($id != 'action' && $value['id'] == 1) {
                $ids[] = $id;
            }
        }

        if (count($ids) == 0 || $action == null) {
            $this->Session->setFlash(__('No items selected.', true));
            $this->redirect(array('action' => 'index', 'vocabulary' => $this->vocabularyId));
        }

        if ($action == 'delete' &&
            $this->Term->deleteAll(array('Term.id' => $ids), true, true)) {
            $this->Session->setFlash(__('Terms deleted.', true));
        } elseif ($action == 'publish' &&
            $this->Term->updateAll(array('Term.status' => 1), array('Term.id' => $ids))) {
            $this->Session->setFlash(__('Terms published', true));
        } elseif ($action == 'unpublish' &&
            $this->Term->updateAll(array('Term.status' => 0), array('Term.id' => $ids))) {
            $this->Session->setFlash(__('Terms unpublished', true));
        } else {
            $this->Session->setFlash(__('An error occurred.', true));
        }

        $this->redirect(array('action' => 'index', 'vocabulary' => $this->vocabularyId));
    }

}
?>