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
	public $name = 'Settings';

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array('Setting');

/**
 * Helpers used by the Controller
 *
 * @var array
 * @access public
 */
	public $helpers = array('Html', 'Form');

	public function admin_dashboard() {
		$this->set('title_for_layout', __('Dashboard', true));
	}

	public function admin_index() {
		$this->set('title_for_layout', __('Settings', true));

		$this->Setting->recursive = 0;
		$this->paginate['Setting']['order'] = "Setting.weight ASC";
		if (isset($this->params['named']['p'])) {
			$this->paginate['Setting']['conditions'] = "Setting.key LIKE '". $this->params['named']['p'] ."%'";
		}
		$this->set('settings', $this->paginate());
	}

	public function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Setting.', true), 'default', array('class' => 'error'));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('setting', $this->Setting->read(null, $id));
	}

	public function admin_add() {
		$this->set('title_for_layout', __('Add Setting', true));

		if (!empty($this->data)) {
			$this->Setting->create();
			if ($this->Setting->save($this->data)) {
				$this->Session->setFlash(__('The Setting has been saved', true), 'default', array('class' => 'success'));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Setting could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
			}
		}
	}

	public function admin_edit($id = null) {
		$this->set('title_for_layout', __('Edit Setting', true));

		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Setting', true), 'default', array('class' => 'error'));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Setting->save($this->data)) {
				$this->Session->setFlash(__('The Setting has been saved', true), 'default', array('class' => 'success'));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Setting could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Setting->read(null, $id);
		}
	}

	public function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Setting', true), 'default', array('class' => 'error'));
			$this->redirect(array('action'=>'index'));
		}
		if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
			$blackHoleCallback = $this->Security->blackHoleCallback;
			$this->$blackHoleCallback();
		}
		if ($this->Setting->delete($id)) {
			$this->Session->setFlash(__('Setting deleted', true), 'default', array('class' => 'success'));
			$this->redirect(array('action'=>'index'));
		}
	}

	public function admin_prefix($prefix = null) {
		$this->set('title_for_layout', sprintf(__('Settings: %s', true), $prefix));

		if(!empty($this->data) && $this->Setting->saveAll($this->data['Setting'])) {
			$this->Session->setFlash(__("Settings updated successfully", true), 'default', array('class' => 'success'));
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
			$this->Session->setFlash(__("Invalid Setting key", true), 'default', array('class' => 'error'));
		}

		$this->set("prefix", $prefix);
	}

	public function admin_moveup($id, $step = 1) {
		if( $this->Setting->moveup($id, $step) ) {
			$this->Session->setFlash(__('Moved up successfully', true), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__('Could not move up', true), 'default', array('class' => 'error'));
		}

		$this->redirect(array('admin' => true, 'controller' => 'settings', 'action' => 'index'));
	}

	public function admin_movedown($id, $step = 1) {
		if( $this->Setting->movedown($id, $step) ) {
			$this->Session->setFlash(__('Moved down successfully', true), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__('Could not move down', true), 'default', array('class' => 'error'));
		}

		$this->redirect(array('admin' => true, 'controller' => 'settings', 'action' => 'index'));
	}

}
