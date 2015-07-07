<?php

namespace Croogo\Settings\Controller\Admin;

use Croogo\Core\Controller\CroogoAppController;

/**
 * Settings Controller
 *
 * @category Settings.Controller
 * @package  Croogo.Settings
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class SettingsController extends CroogoAppController {

/**
 * Initialize
 */
	public function initialize() {
		parent::initialize();

		$this->loadComponent('Search.Prg', [
			'presetForm' => [
				'paramType' => 'querystring',
			],
			'commonProcess' => [
				'paramType' => 'querystring',
				'filterEmpty' => true,
			],
		]);
	}

/**
 * Preset Variables Search
 */
	public $presetVars = true;

/**
 * Admin index
 *
 * @return void
 * @access public
 */
	public function index() {
		$this->Prg->commonProcess();

		$this->paginate = [
			'order' => [
				'Settings.weight' => 'DESC'
			]
		];

		$criteria = $this->Settings->find('searchable', $this->Prg->parsedParams());

		$this->set('settings', $this->paginate($criteria));
	}

/**
 * Admin view
 *
 * @param view $id
 * @return void
 * @access public
 */
	public function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__d('croogo', 'Invalid Setting.'), 'default', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('setting', $this->Setting->read(null, $id));
	}

/**
 * Admin add
 *
 * @return void
 * @access public
 */
	public function add() {
		$setting = $this->Settings->newEntity();

		if ($this->request->is('post')) {
			$setting = $this->Settings->patchEntity($setting, $this->request->data());

			if ($this->Settings->save($setting)) {
				$this->Flash->success(__d('croogo', 'The Setting has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__d('croogo', 'The Setting could not be saved. Please, try again.'));
			}
		}

		$this->set(compact('setting'));
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function edit($id = null) {
		if (!$id) {
			$this->Flash->error(__d('croogo', 'Invalid Setting'));
			return $this->redirect(array('action' => 'index'));
		}

		$setting = $this->Settings->get($id);

		if ($this->request->is('put')) {
			$setting = $this->Settings->patchEntity($setting, $this->request->data());

			if ($this->Settings->save($setting)) {
				$this->Flash->success(__d('croogo', 'The Setting has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__d('croogo', 'The Setting could not be saved. Please, try again.'));
			}
		}

		$this->set(compact('setting'));
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
			$this->Flash->error(__d('croogo', 'Invalid Setting'));
			return $this->redirect(array('action' => 'index'));
		}

		$setting = $this->Settings->get($id);

		if ($this->Settings->delete($setting)) {
			$this->Flash->success(__d('croogo', 'Setting deleted'));
			return $this->redirect(array('action' => 'index'));
		}
	}

/**
 * Admin prefix
 *
 * @param string $prefix
 * @return void
 * @access public
 */
	public function prefix($prefix = null) {
		$this->Settings->addBehavior('Croogo/Core.Params');

		if ($this->request->is('post')) {
			foreach ($this->request->data() as $id => $value) {
				$setting = $this->Settings->get($id);
				$setting->value = $value;
				$this->Settings->save($setting);
			}
			$this->Flash->success(__d('croogo', 'Settings updated successfully'));
			return $this->redirect(['action' => 'prefix', $prefix]);
		}

		$settings = $this->Settings->find('all', [
			'order' => 'Settings.weight ASC',
			'conditions' => [
				'Settings.key LIKE' => $prefix . '.%',
				'Settings.editable' => 1,
			],
		]);

		if ($settings->count() == 0) {
			$this->Flash->error(__d('croogo', 'Invalid Setting key'));
		}

		$this->set(compact('prefix', 'settings'));
	}

/**
 * Admin moveup
 *
 * @param integer $id
 * @param integer $step
 * @return void
 * @access public
 */
	public function moveup($id, $step = 1) {
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
		return $this->redirect($redirect);
	}

/**
 * Admin moveup
 *
 * @param integer $id
 * @param integer $step
 * @return void
 * @access public
 */
	public function movedown($id, $step = 1) {
		if ($this->Setting->moveDown($id, $step)) {
			$this->Session->setFlash(__d('croogo', 'Moved down successfully'), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'Could not move down'), 'default', array('class' => 'error'));
		}

		return $this->redirect(array('admin' => true, 'controller' => 'settings', 'action' => 'index'));
	}

}
