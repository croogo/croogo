<?php
/**
 * Vocabularies Controller
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
class VocabulariesController extends AppController {
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
    public $uses = array('Vocabulary');

    public function admin_index() {
        $this->set('title_for_layout', __('Vocabularies', true));

        $this->Vocabulary->recursive = 0;
        $this->paginate['Vocabulary']['order'] = 'Vocabulary.weight ASC';
        $this->set('vocabularies', $this->paginate());
    }

    public function admin_add() {
        $this->set('title_for_layout', __('Add Vocabulary', true));

        if (!empty($this->data)) {
            $this->Vocabulary->create();
            if ($this->Vocabulary->save($this->data)) {
                $this->Session->setFlash(__('The Vocabulary has been saved', true), 'default', array('class' => 'success'));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Vocabulary could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
            }
        }

        $types = $this->Vocabulary->Type->find('list');
        $this->set(compact('types'));
    }

    public function admin_edit($id = null) {
        $this->set('title_for_layout', __('Edit Vocabulary', true));

        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Vocabulary', true), 'default', array('class' => 'error'));
            $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->data)) {
            if ($this->Vocabulary->save($this->data)) {
                $this->Session->setFlash(__('The Vocabulary has been saved', true), 'default', array('class' => 'success'));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Vocabulary could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Vocabulary->read(null, $id);
        }

        $types = $this->Vocabulary->Type->find('list');
        $this->set(compact('types'));
    }

    public function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Vocabulary', true), 'default', array('class' => 'error'));
            $this->redirect(array('action'=>'index'));
        }
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
        }
        if ($this->Vocabulary->delete($id)) {
            $this->Session->setFlash(__('Vocabulary deleted', true), 'default', array('class' => 'success'));
            $this->redirect(array('action'=>'index'));
        }
    }

    public function admin_moveup($id, $step = 1) {
        if( $this->Vocabulary->moveup($id, $step) ) {
            $this->Session->setFlash(__('Moved up successfully', true), 'default', array('class' => 'success'));
        } else {
            $this->Session->setFlash(__('Could not move up', true), 'default', array('class' => 'error'));
        }

        $this->redirect(array('action' => 'index'));
    }

    public function admin_movedown($id, $step = 1) {
        if( $this->Vocabulary->movedown($id, $step) ) {
            $this->Session->setFlash(__('Moved down successfully', true), 'default', array('class' => 'success'));
        } else {
            $this->Session->setFlash(__('Could not move down', true), 'default', array('class' => 'error'));
        }

        $this->redirect(array('action' => 'index'));
    }

}
?>