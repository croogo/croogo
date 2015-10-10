<?php

namespace Croogo\Extensions\Controller\Admin;

class DashboardController extends AppController {

/**
 * Admin dashboard
 */
	public function index() {
		$this->set('title_for_layout', __d('croogo', 'Dashboard'));
	}

}
