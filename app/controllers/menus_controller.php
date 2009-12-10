<?php
/**
 * Menus Controller
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
class MenusController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    var $name = 'Menus';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    var $uses = array('Menu');

    function admin_index() {
        $this->pageTitle = __('Menus', true);

        $this->Menu->recursive = 0;
        $this->paginate['Menu']['order'] = 'Menu.id ASC';
        $this->set('menus', $this->paginate());
    }

    function admin_add() {
        $this->pageTitle = __("Add Menu", true);

        if (!empty($this->data)) {
            $this->Menu->create();
            if ($this->Menu->save($this->data)) {
                $this->Session->setFlash(__('The Menu has been saved', true));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Menu could not be saved. Please, try again.', true));
            }
        }
    }

    function admin_edit($id = null) {
        $this->pageTitle = __("Edit Menu", true);

        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Menu', true));
            $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->data)) {
            if ($this->Menu->save($this->data)) {
                $this->Session->setFlash(__('The Menu has been saved', true));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Menu could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Menu->read(null, $id);
        }
    }

    function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Menu', true));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->Menu->delete($id)) {
            $this->Session->setFlash(__('Menu deleted', true));
            $this->redirect(array('action'=>'index'));
        }
    }

}
?>