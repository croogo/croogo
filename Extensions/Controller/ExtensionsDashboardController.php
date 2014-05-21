<?php

namespace Croogo\Extensions\Controller;

use Extensions\Controller\ExtensionsAppController;
class ExtensionsDashboardController extends ExtensionsAppController {

/**
 * Admin dashboard
 */
	public function admin_index() {
		$this->set('title_for_layout', __d('croogo', 'Dashboard'));
	}

}
