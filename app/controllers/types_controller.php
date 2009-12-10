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
    var $name = 'Types';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    var $uses = array('Type');

    function admin_index() {
        $this->pageTitle = __('Type', true);

        $this->Type->recursive = 0;
        $this->paginate['Type']['order'] = 'Type.title ASC';
        $this->set('types', $this->paginate());
    }

    function admin_add() {
        $this->pageTitle = __("Add Type", true);

        if (!empty($this->data)) {
            $this->Type->create();
            if ($this->Type->save($this->data)) {
                $this->Session->setFlash(__('The Type has been saved', true));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Type could not be saved. Please, try again.', true));
            }
        }

        $vocabularies = $this->Type->Vocabulary->find('list');
        $this->set(compact('vocabularies'));
    }

    function admin_edit($id = null) {
        $this->pageTitle = __("Edit Type", true);

        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Type', true));
            $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->data)) {
            if ($this->Type->save($this->data)) {
                $this->Session->setFlash(__('The Type has been saved', true));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Type could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Type->read(null, $id);
        }

        $vocabularies = $this->Type->Vocabulary->find('list');
        $this->set(compact('vocabularies'));
    }

    function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Type', true));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->Type->delete($id)) {
            $this->Session->setFlash(__('Tyoe deleted', true));
            $this->redirect(array('action'=>'index'));
        }
    }

}
?>