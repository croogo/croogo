<?php
/**
 * Types Controller
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
class TypesController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    public $name = 'Types';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    public $uses = array('Type');

    public function beforeFilter() {
        parent::beforeFilter();
        if ($this->action == 'admin_edit') {
            $this->Security->disabledFields = array('alias');
        }
    }

    public function admin_index() {
        $this->set('title_for_layout', __('Type', true));

        $this->Type->recursive = 0;
        $this->paginate['Type']['order'] = 'Type.title ASC';
        $this->set('types', $this->paginate());
    }

    public function admin_add() {
        $this->set('title_for_layout', __('Add Type', true));

        if (!empty($this->data)) {
            $this->Type->create();
            if ($this->Type->save($this->data)) {
                $this->Session->setFlash(__('The Type has been saved', true), 'default', array('class' => 'success'));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Type could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
            }
        }

        $vocabularies = $this->Type->Vocabulary->find('list');
        $this->set(compact('vocabularies'));
    }

    public function admin_edit($id = null) {
        $this->set('title_for_layout', __('Edit Type', true));

        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Type', true), 'default', array('class' => 'error'));
            $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->data)) {
            if ($this->Type->save($this->data)) {
                $this->Session->setFlash(__('The Type has been saved', true), 'default', array('class' => 'success'));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Type could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Type->read(null, $id);
        }

        $vocabularies = $this->Type->Vocabulary->find('list');
        $this->set(compact('vocabularies'));
    }

    public function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Type', true), 'default', array('class' => 'error'));
            $this->redirect(array('action'=>'index'));
        }
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
        }
        if ($this->Type->delete($id)) {
            $this->Session->setFlash(__('Type deleted', true), 'default', array('class' => 'success'));
            $this->redirect(array('action'=>'index'));
        }
    }
}
?>