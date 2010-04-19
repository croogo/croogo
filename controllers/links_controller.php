<?php
/**
 * Links Controller
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
class LinksController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    public $name = 'Links';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    public $uses = array(
        'Link',
        'Role',
    );
/**
 * Menu ID
 *
 * holds the current menu ID (if any)
 *
 * @var string
 * @access public
 */
    public $menuId = '';

    public function beforeFilter() {
        parent::beforeFilter();

        if (isset($this->params['named']['menu']) && $this->params['named']['menu'] != null) {
            $menu = $this->params['named']['menu'];
            $this->menuId = $menu;
            $this->Link->Behaviors->attach('Tree', array('scope' => array('Link.menu_id' => $this->menuId)));
        } else {
            $menu = '';
            $this->menuId = $menu;
        }
        $this->set(compact('menu'));
    }

    public function admin_index() {
        $this->set('title_for_layout', __('Links', true));

        $conditions = array();
        if ($this->menuId != null) {
            $menu = $this->Link->Menu->findById($this->menuId);
            $this->set('title_for_layout', sprintf(__('Links: %s', true), $menu['Menu']['title']));
            $conditions['Link.menu_id'] = $this->menuId;
        }

        $this->Link->recursive = 0;
        $linksTree = $this->Link->generatetreelist($conditions);
        $linksStatus = $this->Link->find('list', array(
            'conditions' => $conditions,
            'fields' => array(
                'Link.id',
                'Link.status',
            ),
        ));
        $this->set(compact('linksTree', 'linksStatus'));
    }

    public function admin_add() {
        $this->set('title_for_layout', __('Add Link', true));

        if (!empty($this->data)) {
            $this->Link->create();
            $this->data['Link']['visibility_roles'] = $this->Link->encodeData($this->data['Role']['Role']);
            if ($this->Link->save($this->data)) {
                $this->Session->setFlash(__('The Link has been saved', true));
                $this->redirect(array('action'=>'index', 'menu' => $this->menuId));
            } else {
                $this->Session->setFlash(__('The Link could not be saved. Please, try again.', true));
            }
        }
        $menus = $this->Link->Menu->find('list');
        $roles = $this->Role->find('list');
        $parentConditions = array();
        if ($this->menuId != null) {
            $parentConditions['Link.menu_id'] = $this->menuId;
        }
        $parentLinks = $this->Link->generatetreelist($parentConditions);
        $this->set(compact('menus', 'roles', 'parentLinks'));
    }

    public function admin_edit($id = null) {
        $this->set('title_for_layout', __('Edit Link', true));

        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Link', true));
            $this->redirect(array('action'=>'index', 'menu' => $this->menuId));
        }
        if (!empty($this->data)) {
            $this->data['Link']['visibility_roles'] = $this->Link->encodeData($this->data['Role']['Role']);
            if ($this->Link->save($this->data)) {
                $this->Session->setFlash(__('The Link has been saved', true));
                $this->redirect(array('action'=>'index', 'menu' => $this->menuId));
            } else {
                $this->Session->setFlash(__('The Link could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $data = $this->Link->read(null, $id);
            $data['Role']['Role'] = $this->Link->decodeData($data['Link']['visibility_roles']);
            $this->data = $data;
        }
        $menus = $this->Link->Menu->find('list');
        $roles = $this->Role->find('list');
        $parentConditions = array();
        if ($this->menuId != null) {
            $parentConditions['Link.menu_id'] = $this->menuId;
        }
        $parentLinks = $this->Link->generatetreelist($parentConditions);
        $this->set(compact('menus', 'roles', 'parentLinks'));
    }

    public function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Link', true));
            $this->redirect(array('action'=>'index', 'menu' => $this->menuId));
        }
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
        }
        if ($this->Link->delete($id)) {
            $this->Session->setFlash(__('Link deleted', true));
            $this->redirect(array('action'=>'index', 'menu' => $this->menuId));
        }
    }

    public function admin_moveup($id, $step = 1) {
        if( $this->Link->moveup($id, $step) ) {
            $this->Session->setFlash(__('Moved up successfully', true));
        } else {
            $this->Session->setFlash(__('Could not move up', true));
        }

        $this->redirect(array('admin' => true, 'action' => 'index', 'menu' => $this->menuId));
    }

    public function admin_movedown($id, $step = 1) {
        if( $this->Link->movedown($id, $step) ) {
            $this->Session->setFlash(__('Moved down successfully', true));
        } else {
            $this->Session->setFlash(__('Could not move down', true));
        }

        $this->redirect(array('admin' => true, 'action' => 'index', 'menu' => $this->menuId));
    }

    public function admin_process() {
        $action = $this->data['Link']['action'];
        $ids = array();
        foreach ($this->data['Link'] AS $id => $value) {
            if ($id != 'action' && $value['id'] == 1) {
                $ids[] = $id;
            }
        }

        if (count($ids) == 0 || $action == null) {
            $this->Session->setFlash(__('No items selected.', true));
            $this->redirect(array('action' => 'index', 'menu' => $this->menuId));
        }

        if ($action == 'delete' &&
            $this->Link->deleteAll(array('Link.id' => $ids), true, true)) {
            $this->Session->setFlash(__('Links deleted.', true));
        } elseif ($action == 'publish' &&
            $this->Link->updateAll(array('Link.status' => 1), array('Link.id' => $ids))) {
            $this->Session->setFlash(__('Links published', true));
        } elseif ($action == 'unpublish' &&
            $this->Link->updateAll(array('Link.status' => 0), array('Link.id' => $ids))) {
            $this->Session->setFlash(__('Links unpublished', true));
        } else {
            $this->Session->setFlash(__('An error occurred.', true));
        }

        $this->redirect(array('action' => 'index', 'menu' => $this->menuId));
    }

}
?>