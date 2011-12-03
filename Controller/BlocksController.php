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
    public $name = 'Blocks';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    public $uses = array('Block', 'Role');

    public $paginate = array(
        'limit' => 10,
        );

    public function admin_index() {
        $this->set('title_for_layout', __('Blocks'));

        $this->Block->recursive = 0;
        $this->paginate['Block']['order'] = array('Block.weight' => 'ASC');
        $this->set('blocks', $this->paginate());
    }

    public function admin_add() {
        $this->set('title_for_layout', __('Add Block'));

        if (!empty($this->request->data)) {
            $this->Block->create();
            $this->request->data['Block']['visibility_roles'] = $this->Block->encodeData($this->request->data['Role']['Role']);
            $this->request->data['Block']['visibility_paths'] = $this->Block->encodeData(explode("\n", $this->request->data['Block']['visibility_paths']));
            if ($this->Block->save($this->request->data)) {
                $this->Session->setFlash(__('The Block has been saved'), 'default', array('class' => 'success'));
                if (isset($this->request->data['apply'])) {
                    $this->redirect(array('action'=>'edit', $this->Block->id));
                } else {
                    $this->redirect(array('action'=>'index'));
                }
            } else {
                $this->Session->setFlash(__('The Block could not be saved. Please, try again.'), 'default', array('class' => 'error'));
            }
        }
        $regions = $this->Block->Region->find('list');
        $roles   = $this->Role->find('list');
        $this->set(compact('regions', 'roles'));
    }

    public function admin_edit($id = null) {
        $this->set('title_for_layout', __('Edit Block'));

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid Block'), 'default', array('class' => 'error'));
            $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->request->data)) {
            $this->request->data['Block']['visibility_roles'] = $this->Block->encodeData($this->request->data['Role']['Role']);
            $this->request->data['Block']['visibility_paths'] = $this->Block->encodeData(explode("\n", $this->request->data['Block']['visibility_paths']));
            if ($this->Block->save($this->request->data)) {
                $this->Session->setFlash(__('The Block has been saved'), 'default', array('class' => 'success'));
                if (! isset($this->request->data['apply'])) {
                    $this->redirect(array('action'=>'index'));
                }
            } else {
                $this->Session->setFlash(__('The Block could not be saved. Please, try again.'), 'default', array('class' => 'error'));
            }
        }
        if (empty($this->request->data)) {
            $data = $this->Block->read(null, $id);
            $data['Role']['Role'] = $this->Block->decodeData($data['Block']['visibility_roles']);
            if ($data['Block']['visibility_paths'] != '') {
                $data['Block']['visibility_paths'] = implode("\n", $this->Block->decodeData($data['Block']['visibility_paths']));
            }
            $this->request->data = $data;
        }
        $regions = $this->Block->Region->find('list');
        $roles = $this->Role->find('list');
        $this->set(compact('regions', 'roles'));
    }

    public function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Block'), 'default', array('class' => 'error'));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->Block->delete($id)) {
            $this->Session->setFlash(__('Block deleted'), 'default', array('class' => 'success'));
            $this->redirect(array('action'=>'index'));
        }
    }

    public function admin_moveup($id, $step = 1) {
        if( $this->Block->moveup($id, $step) ) {
            $this->Session->setFlash(__('Moved up successfully'), 'default', array('class' => 'success'));
        } else {
            $this->Session->setFlash(__('Could not move up'), 'default', array('class' => 'error'));
        }

        $this->redirect(array('action' => 'index'));
    }

    public function admin_movedown($id, $step = 1) {
        if( $this->Block->movedown($id, $step) ) {
            $this->Session->setFlash(__('Moved down successfully'), 'default', array('class' => 'success'));
        } else {
            $this->Session->setFlash(__('Could not move down'), 'default', array('class' => 'error'));
        }

        $this->redirect(array('action' => 'index'));
    }

    public function admin_process() {
        $action = $this->request->data['Block']['action'];
        $ids = array();
        foreach ($this->request->data['Block'] AS $id => $value) {
            if ($id != 'action' && $value['id'] == 1) {
                $ids[] = $id;
            }
        }

        if (count($ids) == 0 || $action == null) {
            $this->Session->setFlash(__('No items selected.'), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
        }

        if ($action == 'delete' &&
            $this->Block->deleteAll(array('Block.id' => $ids), true, true)) {
            $this->Session->setFlash(__('Blocks deleted.'), 'default', array('class' => 'success'));
        } elseif ($action == 'publish' &&
            $this->Block->updateAll(array('Block.status' => 1), array('Block.id' => $ids))) {
            $this->Session->setFlash(__('Blocks published'), 'default', array('class' => 'success'));
        } elseif ($action == 'unpublish' &&
            $this->Block->updateAll(array('Block.status' => 0), array('Block.id' => $ids))) {
            $this->Session->setFlash(__('Blocks unpublished'), 'default', array('class' => 'success'));
        } else {
            $this->Session->setFlash(__('An error occurred.'), 'default', array('class' => 'error'));
        }

        $this->redirect(array('action' => 'index'));
    }

}
?>