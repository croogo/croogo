<?php

App::uses('DashboardsAppController', 'Dashboards.Controller');

/**
 * DashboardBoxes Controller
 *
 * @category Controller
 * @package  Croogo.Dashboards.Controller
 * @version  2.2
 * @author   Walther Lalk <emailme@waltherlalk.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class DashboardBoxesController extends DashboardsAppController {

	public $helpers = array(
		'Dashboards.Dashboard'
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
		$this->set('boxes_for_dashboard', $this->DashboardBox->find('all', array(
			'fields' => array(
				'DashboardBox.alias',
				'DashboardBox.collapsed',
				'DashboardBox.column',
				'DashboardBox.order'
			),
			'order' => array(
				'DashboardBox.order'
			)
		)));
	}

	public function admin_save() {
		$userId = $this->Auth->user('id');
		$data = Hash::insert($this->request->data['dashboard'], '{n}.user_id', $userId);
		$this->DashboardBox->deleteAll(array('user_id' => $userId));
		$this->DashboardBox->saveMany($data);
	}

}
