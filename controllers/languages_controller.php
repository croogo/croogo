<?php
/**
 * Languages Controller
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
class LanguagesController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    public $name = 'Languages';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    public $uses = array('Language');

    public function admin_index() {
        $this->set('title_for_layout', __('Languages', true));

        $this->Language->recursive = 0;
        $this->paginate['Language']['order'] = 'Language.weight ASC';
        $this->set('languages', $this->paginate());
    }

    public function admin_add() {
        $this->set('title_for_layout', __("Add Language", true));

        if (!empty($this->data)) {
            $this->Language->create();
            if ($this->Language->save($this->data)) {
                $this->Session->setFlash(__('The Language has been saved', true), 'default', array('class' => 'success'));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Language could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
            }
        }
    }

    public function admin_edit($id = null) {
        $this->set('title_for_layout', __("Edit Language", true));

        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Language', true), 'default', array('class' => 'error'));
            $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->data)) {
            if ($this->Language->save($this->data)) {
                $this->Session->setFlash(__('The Language has been saved', true), 'default', array('class' => 'success'));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Language could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Language->read(null, $id);
        }
    }

    public function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Language', true), 'default', array('class' => 'error'));
            $this->redirect(array('action'=>'index'));
        }
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
        }
        if ($this->Language->delete($id)) {
            $this->Session->setFlash(__('Language deleted', true), 'default', array('class' => 'success'));
            $this->redirect(array('action'=>'index'));
        }
    }

    public function admin_moveup($id, $step = 1) {
        if ($this->Language->moveup($id, $step)) {
            $this->Session->setFlash(__('Moved up successfully', true), 'default', array('class' => 'success'));
        } else {
            $this->Session->setFlash(__('Could not move up', true), 'default', array('class' => 'error'));
        }

        $this->redirect(array('action' => 'index'));
    }

    public function admin_movedown($id, $step = 1) {
        if ($this->Language->movedown($id, $step)) {
            $this->Session->setFlash(__('Moved down successfully', true), 'default', array('class' => 'success'));
        } else {
            $this->Session->setFlash(__('Could not move down', true), 'default', array('class' => 'error'));
        }

        $this->redirect(array('action' => 'index'));
    }

    public function admin_select($id = null, $modelAlias = null) {
        if ($id == null ||
            $modelAlias == null) {
            $this->redirect(array('action' => 'index'));
        }

        $this->set('title_for_layout', __('Select a language', true));
        $languages = $this->Language->find('all', array(
            'conditions' => array(
                'Language.status' => 1,
            ),
            'order' => 'Language.weight ASC',
        ));
        $this->set(compact('id', 'modelAlias', 'languages'));
    }

}
?>