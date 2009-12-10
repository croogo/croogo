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
    var $name = 'Languages';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    var $uses = array('Language');

    function admin_index() {
        $this->pageTitle = __('Languages', true);

        $this->Language->recursive = 0;
        $this->paginate['Language']['order'] = 'Language.weight ASC';
        $this->set('languages', $this->paginate());
    }

    function admin_add() {
        $this->pageTitle = __("Add Language", true);

        if (!empty($this->data)) {
            $this->Language->create();
            if ($this->Language->save($this->data)) {
                $this->Session->setFlash(__('The Language has been saved', true));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Language could not be saved. Please, try again.', true));
            }
        }
    }

    function admin_edit($id = null) {
        $this->pageTitle = __("Edit Language", true);

        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Language', true));
            $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->data)) {
            if ($this->Language->save($this->data)) {
                $this->Session->setFlash(__('The Language has been saved', true));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Language could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Language->read(null, $id);
        }
    }

    function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Language', true));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->Language->delete($id)) {
            $this->Session->setFlash(__('Language deleted', true));
            $this->redirect(array('action'=>'index'));
        }
    }

    function admin_moveup($id, $step = 1) {
        if ($this->Language->moveup($id, $step)) {
            $this->Session->setFlash(__('Moved up succuessfully', true));
        } else {
            $this->Session->setFlash(__('Could not move up', true));
        }

        $this->redirect(array('action' => 'index'));
    }

    function admin_movedown($id, $step = 1) {
        if ($this->Language->movedown($id, $step)) {
            $this->Session->setFlash(__('Moved down succuessfully', true));
        } else {
            $this->Session->setFlash(__('Could not move down', true));
        }

        $this->redirect(array('action' => 'index'));
    }

    function admin_select($controller = null, $action = null, $id = null) {
        if ($controller == null ||
            $action == null ||
            $id == null) {
            $this->redirect(array('action' => 'index'));
        }

        $this->pageTitle = __('Select a language', true);
        $languages = $this->Language->find('all', array(
            'conditions' => array(
                'Language.status' => 1,
            ),
            'order' => 'Language.weight ASC',
        ));
        $this->set(compact('controller', 'action', 'id', 'languages'));
    }

}
?>