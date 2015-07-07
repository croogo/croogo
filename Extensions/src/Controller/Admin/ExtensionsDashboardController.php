<?php

namespace Croogo\Extensions\Controller\Admin;

use Croogo\Extensions\Controller\ExtensionsAppController;
class ExtensionsDashboardController extends ExtensionsAppController {

/**
 * Admin dashboard
 */
	public function index() {
		$this->set('title_for_layout', __d('croogo', 'Dashboard'));
	}

}
