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
	}

/**
 * Admin dashboard
 */
	public function admin_index() {
		$this->set('title_for_layout', __d('croogo', 'Dashboard'));
		$Dashboard = $this->DashboardsDashboard;
		$this->set('boxes_for_dashboard', $Dashboard->find('all', array(
			'fields' => array(
				$Dashboard->escapeField('alias'),
				$Dashboard->escapeField('collapsed'),
				$Dashboard->escapeField('column'),
				$Dashboard->escapeField('order'),
			),
			'order' => array(
				$Dashboard->escapeField('order'),
			),
		)));
	}

	public function admin_save() {
		$userId = $this->Auth->user('id');
		$data = Hash::insert($this->request->data['dashboard'], '{n}.user_id', $userId);
		$this->DashboardsDashboard->deleteAll(array('user_id' => $userId));
		$this->DashboardsDashboard->saveMany($data);
	}

}
