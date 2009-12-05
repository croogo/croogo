<?php
/**
 * Blocks Controller
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
class BlocksController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    var $name = 'Blocks';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    var $uses = array('Block', 'Role');

    function admin_index() {
        $this->pageTitle = __('Blocks', true);

        $this->Block->recursive = 0;
        $this->paginate['Block']['order'] = array('Block.region_id' => 'ASC', 'Block.weight' => 'ASC');
        $this->set('blocks', $this->paginate());
    }

    function admin_add() {
        $this->pageTitle = __("Add Block", true);

        if (!empty($this->data)) {
            $this->Block->create();
            $this->data['Block']['visibility_roles'] = $this->Block->encodeData($this->data['Role']['Role']);
            $this->data['Block']['visibility_paths'] = $this->Block->encodeData(explode("\n", $this->data['Block']['visibility_paths']));
            if ($this->Block->save($this->data)) {
                $this->Session->setFlash(__('The Block has been saved', true));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Block could not be saved. Please, try again.', true));
            }
        }
        $regions = $this->Block->Region->find('list');
        $roles   = $this->Role->find('list');
        $this->set(compact('regions', 'roles'));
    }

    function admin_edit($id = null) {
        $this->pageTitle = __("Edit Block", true);

        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Block', true));
            $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->data)) {
            $this->data['Block']['visibility_roles'] = $this->Block->encodeData($this->data['Role']['Role']);
            $this->data['Block']['visibility_paths'] = $this->Block->encodeData(explode("\n", $this->data['Block']['visibility_paths']));
            if ($this->Block->save($this->data)) {
                $this->Session->setFlash(__('The Block has been saved', true));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Block could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $data = $this->Block->read(null, $id);
            $data['Role']['Role'] = $this->Block->decodeData($data['Block']['visibility_roles']);
            if ($data['Block']['visibility_paths'] != '') {
                $data['Block']['visibility_paths'] = implode("\n", $this->Block->decodeData($data['Block']['visibility_paths']));
            }
            $this->data = $data;
        }
        $regions = $this->Block->Region->find('list');
        $roles = $this->Role->find('list');
        $this->set(compact('regions', 'roles'));
    }

    function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Block', true));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->Block->del($id)) {
            $this->Session->setFlash(__('Block deleted', true));
            $this->redirect(array('action'=>'index'));
        }
    }

    function admin_moveup($id, $step = 1) {
        if( $this->Block->moveup($id, $step) ) {
            $this->Session->setFlash(__("Moved up succuessfully", true));
        } else {
            $this->Session->setFlash(__("Could not move up", true));
        }

        $this->redirect(array('action' => 'index'));
    }

    function admin_movedown($id, $step = 1) {
        if( $this->Block->movedown($id, $step) ) {
            $this->Session->setFlash(__("Moved down succuessfully", true));
        } else {
            $this->Session->setFlash(__("Could not move down", true));
        }

        $this->redirect(array('action' => 'index'));
    }

    function admin_process() {
        $action = $this->data['Block']['action'];
        $ids = array();
        foreach ($this->data['Block'] AS $id => $value) {
            if ($id != 'action' && $value['id'] == 1) {
                $ids[] = $id;
            }
        }

        if (count($ids) == 0 || $action == null) {
            $this->Session->setFlash(__('No items selected.', true));
            $this->redirect(array('action' => 'index'));
        }

        if ($action == 'delete' &&
            $this->Block->deleteAll(array('Block.id' => $ids), true, true)) {
            $this->Session->setFlash(__('Blocks deleted.', true));
        } elseif ($action == 'publish' &&
            $this->Block->updateAll(array('Block.status' => 1), array('Block.id' => $ids))) {
            $this->Session->setFlash(__('Blocks published', true));
        } elseif ($action == 'unpublish' &&
            $this->Block->updateAll(array('Block.status' => 0), array('Block.id' => $ids))) {
            $this->Session->setFlash(__('Blocks unpublished', true));
        } else {
            $this->Session->setFlash(__('An error occurred.', true));
        }

        $this->redirect(array('action' => 'index'));
    }

}
?>