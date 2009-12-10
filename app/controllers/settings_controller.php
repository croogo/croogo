<?php
/**
 * Settings Controller
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
class SettingsController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    var $name = 'Settings';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    var $uses = array('Setting');
/**
 * Helpers used by the Controller
 *
 * @var array
 * @access public
 */
    var $helpers = array('Html', 'Form');

    function admin_dashboard() {
        $this->pageTitle = __('Dashboard', true);
    }

    function admin_index() {
        $this->pageTitle = "Settings";

        $this->Setting->recursive = 0;
        $this->paginate['Setting']['order'] = "Setting.weight ASC";
        if (isset($this->params['named']['p'])) {
            $this->paginate['Setting']['conditions'] = "Setting.key LIKE '". $this->params['named']['p'] ."%'";
        }
        $this->set('settings', $this->paginate());
    }

    function admin_view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid Setting.', true));
            $this->redirect(array('action'=>'index'));
        }
        $this->set('setting', $this->Setting->read(null, $id));
    }

    function admin_add() {
        $this->pageTitle = "Add Setting";

        if (!empty($this->data)) {
            $this->Setting->create();
            if ($this->Setting->save($this->data)) {
                $this->Session->setFlash(__('The Setting has been saved', true));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Setting could not be saved. Please, try again.', true));
            }
        }
    }

    function admin_edit($id = null) {
        $this->pageTitle = "Edit Setting";

        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Setting', true));
            $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->data)) {
            if ($this->Setting->save($this->data)) {
                $this->Session->setFlash(__('The Setting has been saved', true));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Setting could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Setting->read(null, $id);
        }
    }

    function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Setting', true));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->Setting->delete($id)) {
            $this->Session->setFlash(__('Setting deleted', true));
            $this->redirect(array('action'=>'index'));
        }
    }

    function admin_prefix($prefix = null) {
        $this->pageTitle = __('Settings', true) . ': ' . $prefix;

        if(!empty($this->data) && $this->Setting->saveAll($this->data['Setting'])) {
            $this->Session->setFlash(__("Settings updated successfully", true));
        }

        $settings = $this->Setting->find('all', array(
            'order' => 'Setting.weight ASC',
            'conditions' => array(
                'Setting.key LIKE' => $prefix . '.%',
                'Setting.editable' => 1,
            ),
        ));
            //'conditions' => "Setting.key LIKE '".$prefix."%'"));
        $this->set(compact('settings'));

        if( count($settings) == 0 ) {
            $this->Session->setFlash(__("Invalid Setting key", true));
        }

        $this->set("prefix", $prefix);
    }

    function admin_moveup($id, $step = 1) {
        if( $this->Setting->moveup($id, $step) ) {
            $this->Session->setFlash(__("Moved up succuessfully", true));
        } else {
            $this->Session->setFlash(__("Could not move up", true));
        }

        $this->redirect(array('admin' => true, 'controller' => 'settings', 'action' => 'index'));
    }

    function admin_movedown($id, $step = 1) {
        if( $this->Setting->movedown($id, $step) ) {
            $this->Session->setFlash(__("Moved down succuessfully", true));
        } else {
            $this->Session->setFlash(__("Could not move down", true));
        }

        $this->redirect(array('admin' => true, 'controller' => 'settings', 'action' => 'index'));
    }

}
?>