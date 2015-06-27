<?php

App::uses('DashboardsAppController', 'Dashboards.Controller');

/**
 * DashboardsDashboards Controller
 *
 * @category Controller
 * @package  Croogo.Dashboards.Controller
 * @version  2.2
 * @author   Walther Lalk <emailme@waltherlalk.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class DashboardsDashboardsController extends DashboardsAppController {

	public $helpers = array(
		'Dashboards.Dashboards',
	);

/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Security->unlockedActions[] = 'admin_save';
		$this->Security->unlockedActions[] = 'admin_toggle';
	}

/**
 * Dashboard index
 *
 * @return void
 */
	public function admin_index() {
		$Dashboard = $this->DashboardsDashboard;
		$this->paginate = array(
			'conditions' => array(
				$Dashboard->escapeField('user_id') => $this->Auth->user('id'),
			),
		);
		$dashboards = $this->paginate();
		$this->set(compact('dashboards'));
	}

/**
 * Admin dashboard
 *
 * @return void
 */
	public function admin_dashboard() {
		$this->set('title_for_layout', __d('croogo', 'Dashboard'));
		$Dashboard = $this->DashboardsDashboard;
		$this->set('boxes_for_dashboard', $Dashboard->find('all', array(
			'fields' => array(
				$Dashboard->escapeField('alias'),
				$Dashboard->escapeField('collapsed'),
				$Dashboard->escapeField('status'),
				$Dashboard->escapeField('column'),
				$Dashboard->escapeField('weight'),
			),
			'conditions' => array(
				$Dashboard->escapeField('user_id') => $this->Auth->user('id'),
			),
			'order' => array(
				$Dashboard->escapeField('weight'),
			),
		)));
	}

/**
 * Saves dashboard setting
 *
 * @return void
 */
	public function admin_save() {
		$userId = $this->Auth->user('id');
		if (!$userId) {
			throw new CakeException('You must be logged in');
		}
		$data = Hash::insert($this->request->data['dashboard'], '{n}.user_id', $userId);
		$this->DashboardsDashboard->deleteAll(array('user_id' => $userId));
		$this->DashboardsDashboard->saveMany($data);
	}

/**
 * Delete a dashboard
 *
 * @param int $id Dashboard id
 * @return void
 */
	public function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Dashboard'), 'flash', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}
		if ($this->DashboardsDashboard->delete($id)) {
			$this->Session->setFlash(__d('croogo', 'Dashboard deleted'), 'flash', array('class' => 'success'));
			return $this->redirect($this->referer());
		}
	}

/**
 * Toggle dashboard status
 *
 * @param int $id Dashboard id
 * @param int $status Status
 * @return void
 */
	public function admin_toggle($id = null, $status = null) {
		$this->Croogo->fieldToggle($this->{$this->modelClass}, $id, $status);
	}

/**
 * Admin moveup
 *
 * @param integer $id Dashboard Id
 * @param integer $step Step
 * @return void
 */
	public function admin_moveup($id, $step = 1) {
		if ($this->DashboardsDashboard->moveUp($id, $step)) {
			$this->Session->setFlash(__d('croogo', 'Moved up successfully'), 'flash', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'Could not move up'), 'flash', array('class' => 'error'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * Admin movedown
 *
 * @param integer $id Dashboard Id
 * @param integer $step Step
 * @return void
 */
	public function admin_movedown($id, $step = 1) {
		if ($this->DashboardsDashboard->moveDown($id, $step)) {
			$this->Session->setFlash(__d('croogo', 'Moved down successfully'), 'flash', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'Could not move down'), 'flash', array('class' => 'error'));
		}

		return $this->redirect(array('action' => 'index'));
	}

}
