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
        $this->set('title_for_layout', __('Vocabulary', true));

        $this->Vocabulary->recursive = 0;
        $this->paginate['Vocabulary']['order'] = 'Vocabulary.title ASC';
        $this->set('vocabularies', $this->paginate());
    }

    public function admin_add() {
        $this->set('title_for_layout', __('Add Vocabulary', true));

        if (!empty($this->data)) {
            $this->Vocabulary->create();
            if ($this->Vocabulary->save($this->data)) {
                $this->Session->setFlash(__('The Vocabulary has been saved', true));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Vocabulary could not be saved. Please, try again.', true));
            }
        }

        $types = $this->Vocabulary->Type->find('list');
        $this->set(compact('types'));
    }

    public function admin_edit($id = null) {
        $this->set('title_for_layout', __('Edit Vocabulary', true));

        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Vocabulary', true));
            $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->data)) {
            if ($this->Vocabulary->save($this->data)) {
                $this->Session->setFlash(__('The Vocabulary has been saved', true));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Vocabulary could not be saved. Please, try again.', true));
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
            $this->Session->setFlash(__('Invalid id for Vocabulary', true));
            $this->redirect(array('action'=>'index'));
        }
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
        }
        if ($this->Vocabulary->delete($id)) {
            $this->Session->setFlash(__('Vocabulary deleted', true));
            $this->redirect(array('action'=>'index'));
        }
    }

}
?>