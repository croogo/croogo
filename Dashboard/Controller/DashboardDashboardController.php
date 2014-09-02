<?php

App::uses('DashboardAppController', 'Dashboard.Controller');

/**
 * DashboardApp Controller
 *
 * @category Controller
 * @package  Croogo.Dashboard.Controller
 * @version  2.2
 * @author   Walther Lalk <emailme@waltherlalk.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class DashboardDashboardController extends DashboardAppController {

	public $helpers = array(
		'Dashboard.Dashboard'
	);

/**
 * Admin dashboard
 */
	public function admin_index() {
		$this->set('title_for_layout', __d('croogo', 'Dashboard'));
	}

}
