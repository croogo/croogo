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
 * Admin dashboard
 */
	public function admin_index() {
		$this->set('title_for_layout', __d('croogo', 'Dashboard'));
	}

}
