<?php

/**
 * Menus Helper
 *
 * PHP version 5
 *
 * @category Menus.View/Helper
 * @package  Croogo.Menus
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MenusHelper extends AppHelper {

/**
 * beforeRender
 */
	public function beforeRender($viewFile) {
		if (isset($this->request->params['admin']) && !$this->request->is('ajax')) {
			$this->_adminMenu();
		}
	}

/**
 * Inject admin menu items
 */
	protected function _adminMenu() {
		if (empty($this->_View->viewVars['menus_for_admin_layout'])) {
			return;
		}
		$menus = $this->_View->viewVars['menus_for_admin_layout'];
		foreach ($menus as $m):
			$weight = 9999 + $m['Menu']['weight'];
			CroogoNav::add('menus.children.' . $m['Menu']['alias'], array(
				'title' => $m['Menu']['title'],
				'url' => array(
					'plugin' => 'menus',
					'admin' => true,
					'controller' => 'links',
					'action' => 'index',
					$m['Menu']['id'],
					),
				'weight' => $weight,
				));
		endforeach;
	}

}
