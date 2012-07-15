<?php

/**
 * Meta Helper
 *
 * PHP version 5
 *
 * @category Meta.View/Helper
 * @package  Croogo.Meta
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MetaHelper extends AppHelper {

/**
 * beforeRender
 */
	public function beforeRender($viewFile) {
		if (isset($this->request->params['admin'])) {
			$this->_adminTabs();
		}
	}

/**
 * Hook admin tabs
 */
	protected function _adminTabs() {
		$controller = Inflector::camelize($this->request->params['controller']);
		$title = __('Custom Fields');
		$element = 'Meta.admin/meta_tab';
		Croogo::hookAdminTab("$controller/admin_add", $title, $element);
		Croogo::hookAdminTab("$controller/admin_edit", $title, $element);
	}

}
