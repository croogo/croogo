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
	public $name = 'Menus';

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array('Menu');

	public function admin_index() {
		$this->set('title_for_layout', __('Menus', true));

		$this->Menu->recursive = 0;
		$this->paginate['Menu']['order'] = 'Menu.id ASC';
		$this->set('menus', $this->paginate());
	}

	public function admin_add() {
		$this->set('title_for_layout', __('Add Menu', true));

		if (!empty($this->data)) {
			$this->Menu->create();
			if ($this->Menu->save($this->data)) {
				$this->Session->setFlash(__('The Menu has been saved', true), 'default', array('class' => 'success'));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Menu could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
			}
		}
	}

	public function admin_edit($id = null) {
		$this->set('title_for_layout', __('Edit Menu', true));

		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Menu', true), 'default', array('class' => 'error'));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Menu->save($this->data)) {
				$this->Session->setFlash(__('The Menu has been saved', true), 'default', array('class' => 'success'));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Menu could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Menu->read(null, $id);
		}
	}

	public function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Menu', true), 'default', array('class' => 'error'));
			$this->redirect(array('action'=>'index'));
		}
		if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
			$blackHoleCallback = $this->Security->blackHoleCallback;
			$this->$blackHoleCallback();
		}
		if ($this->Menu->delete($id)) {
			$this->Session->setFlash(__('Menu deleted', true), 'default', array('class' => 'success'));
			$this->redirect(array('action'=>'index'));
		}
	}

}
