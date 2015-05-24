<?php

namespace Croogo\Menus\Controller\Admin;

use Croogo\Menus\Controller\MenusAppController;

/**
 * Menus Controller
 *
 * @category Controller
 * @package  Croogo.Menus.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MenusController extends MenusAppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'Menus';

/**
 * afterConstruct
 */
	public function afterConstruct() {
		parent::afterConstruct();
		$this->_setupAclComponent();
	}

/**
 * beforeFilter
 *
 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Security->unlockedActions[] = 'toggle';
	}

/**
 * Toggle Link status
 *
 * @param $id string Link id
 * @param $status integer Current Link status
 * @return void
 */
	public function admin_toggle($id = null, $status = null) {
		$this->Croogo->fieldToggle($this->Menu, $id, $status);
	}

/**
 * Admin index
 *
 * @return void
 * @access public
 */
	public function index() {
		$this->set('title_for_layout', __d('croogo', 'Menus'));

		$this->Menu->recursive = 0;
		$this->paginate['Menu']['order'] = 'Menu.id ASC';
		$this->set('menus', $this->paginate());
	}

/**
 * Admin add
 *
 * @return void
 * @access public
 */
	public function add() {
		$this->set('title_for_layout', __d('croogo', 'Add Menu'));

		if (!empty($this->request->data)) {
			$this->Menu->create();
			if ($this->Menu->save($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'The Menu has been saved'), 'default', array('class' => 'success'));
				$this->Croogo->redirect(array('action' => 'edit', $this->Menu->id));
			} else {
				$this->Session->setFlash(__d('croogo', 'The Menu could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function edit($id = null) {
		$this->set('title_for_layout', __d('croogo', 'Edit Menu'));

		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__d('croogo', 'Invalid Menu'), 'default', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Menu->save($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'The Menu has been saved'), 'default', array('class' => 'success'));
				$this->Croogo->redirect(array('action' => 'edit', $this->Menu->id));
			} else {
				$this->Session->setFlash(__d('croogo', 'The Menu could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Menu->read(null, $id);
		}
	}

/**
 * Admin delete
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Menu'), 'default', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}
		if ($this->Menu->delete($id)) {
			$this->Session->setFlash(__d('croogo', 'Menu deleted'), 'default', array('class' => 'success'));
			return $this->redirect(array('action' => 'index'));
		}
	}

}
