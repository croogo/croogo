<?php
/**
 * Roles Controller
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
class RolesController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    public $name = 'Roles';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    public $uses = array('Role');

    public function admin_index() {
        $this->set('title_for_layout', __('Roles', true));

        $this->Role->recursive = 0;
        $this->paginate['Role']['order'] = "Role.id ASC";
        $this->set('roles', $this->paginate());
    }

    public function admin_add() {
        $this->set('title_for_layout', __('Add Role', true));

        if (!empty($this->data)) {
            $this->Role->create();
            if ($this->Role->save($this->data)) {
                $this->Session->setFlash(__('The Role has been saved', true), 'default', array('class' => 'success'));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Role could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
            }
        }
    }

    public function admin_edit($id = null) {
        $this->set('title_for_layout', __('Edit Role', true));

        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Role', true), 'default', array('class' => 'error'));
            $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->data)) {
            if ($this->Role->save($this->data)) {
                $this->Session->setFlash(__('The Role has been saved', true), 'default', array('class' => 'success'));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Role could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Role->read(null, $id);
        }
    }

    public function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Role', true), 'default', array('class' => 'error'));
            $this->redirect(array('action'=>'index'));
        }
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
        }
        if ($this->Role->delete($id)) {
            $this->Session->setFlash(__('Role deleted', true), 'default', array('class' => 'success'));
            $this->redirect(array('action'=>'index'));
        }
    }

}
?>