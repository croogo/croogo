<?php

App::uses('SettingsAppController', 'Settings.Controller');

/**
 * Settings Controller
 *
 * PHP version 5
 *
 * @category Settings.Controller
 * @package  Croogo.Settings
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class SettingsController extends SettingsAppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'Settings';

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array('Settings.Setting');

/**
 * Helpers used by the Controller
 *
 * @var array
 * @access public
 */
	public $helpers = array('Html', 'Form');

/**
 * Admin dashboard
 *
 * @return void
 * @access public
 */
	public function admin_dashboard() {
		$this->set('title_for_layout', __d('croogo', 'Dashboard'));
	}

/**
 * Admin index
 *
 * @return void
 * @access public
 */
	public function admin_index() {
		$this->set('title_for_layout', __d('croogo', 'Settings'));

		$this->Setting->recursive = 0;
		$this->paginate['Setting']['order'] = "Setting.weight ASC";
		if (isset($this->request->params['named']['p'])) {
			$this->paginate['Setting']['conditions'] = "Setting.key LIKE '" . $this->request->params['named']['p'] . "%'";
		}
		$this->set('settings', $this->paginate());
	}

/**
 * Admin view
 *
 * @param view $id
 * @return void
 * @access public
 */
	public function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__d('croogo', 'Invalid Setting.'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('setting', $this->Setting->read(null, $id));
	}

/**
 * Admin add
 *
 * @return void
 * @access public
 */
	public function admin_add() {
		$this->set('title_for_layout', __d('croogo', 'Add Setting'));

		if (!empty($this->request->data)) {
			$this->Setting->create();
			if ($this->Setting->save($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'The Setting has been saved'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__d('croogo', 'The Setting could not be saved. Please, try again.'), 'default', array('class' => 'error'));
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
	public function admin_edit($id = null) {
		$this->set('title_for_layout', __d('croogo', 'Edit Setting'));

		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__d('croogo', 'Invalid Setting'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Setting->save($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'The Setting has been saved'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__d('croogo', 'The Setting could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Setting->read(null, $id);
		}
	}

/**
 * Admin delete
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Setting'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Setting->delete($id)) {
			$this->Session->setFlash(__d('croogo', 'Setting deleted'), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		}
	}

/**
 * Admin prefix
 *
 * @param string $prefix
 * @return void
 * @access public
 */
	public function admin_prefix($prefix = null) {
		$this->set('title_for_layout', __d('croogo', 'Settings: %s', $prefix));

		$this->Setting->Behaviors->attach('Params');
		if (!empty($this->request->data) && $this->Setting->saveAll($this->request->data['Setting'])) {
			$this->Session->setFlash(__d('croogo', "Settings updated successfully"), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'prefix', $prefix));
		}

		$settings = $this->Setting->find('all', array(
			'order' => 'Setting.weight ASC',
			'conditions' => array(
				'Setting.key LIKE' => $prefix . '.%',
				'Setting.editable' => 1,
			),
		));
		$this->set(compact('settings'));

		if (count($settings) == 0) {
			$this->Session->setFlash(__d('croogo', "Invalid Setting key"), 'default', array('class' => 'error'));
		}

		$this->set("prefix", $prefix);
	}

/**
 * Admin moveup
 *
 * @param integer $id
 * @param integer $step
 * @return void
 * @access public
 */
	public function admin_moveup($id, $step = 1) {
		if ($this->Setting->moveUp($id, $step)) {
			$this->Session->setFlash(__d('croogo', 'Moved up successfully'), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'Could not move up'), 'default', array('class' => 'error'));
		}

		if (!$redirect = $this->referer()) {
			$redirect = array(
				'admin' => true,
				'plugin' => 'settings',
				'controller' => 'settings',
				'action' => 'index'
			);
		}
		$this->redirect($redirect);
	}

/**
 * Admin moveup
 *
 * @param integer $id
 * @param integer $step
 * @return void
 * @access public
 */
	public function admin_movedown($id, $step = 1) {
		if ($this->Setting->moveDown($id, $step)) {
			$this->Session->setFlash(__d('croogo', 'Moved down successfully'), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'Could not move down'), 'default', array('class' => 'error'));
		}

		$this->redirect(array('admin' => true, 'controller' => 'settings', 'action' => 'index'));
	}

}
