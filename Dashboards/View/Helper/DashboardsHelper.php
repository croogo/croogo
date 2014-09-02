<?php

App::uses('AppHelper', 'View/Helper');

/**
 * Dashboards Helper
 *
 * @category Helper
 * @package  Croogo.Dashboards.View.Helper
 * @version  2.2
 * @author   Walther Lalk <emailme@waltherlalk.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class DashboardsHelper extends AppHelper {

	public $helpers = array(
		'Croogo.Layout',
	);

	public function dashboards($options = array()) {
		$dashboards = Configure::read('Dashboards');
		$userId = AuthComponent::user('id');
		if (empty($userId)) {
			return '';
		}

		$out = null;
		$sorted = Hash::sort($dashboards, '{s}.weight', 'ASC');
		if (empty($this->Role)) {
			$this->Role = ClassRegistry::init('Users.Role');
			$this->Role->Behaviors->attach('Croogo.Aliasable');
		}
		$currentRole = $this->Role->byId($this->Layout->getRoleId());

		$cssSetting = $this->Layout->themeSetting('css');

		foreach ($sorted as $dashboard) {
			if ($currentRole != 'admin' && !in_array($currentRole, $dashboard['access'])) {
				continue;
			}

			$opt = array(
				'dashboard' => $dashboard,
				'class' => $cssSetting['dashboardFull'],
			);
			$out .= $this->_View->element('Extensions.admin/dashboard', $opt);
		}

		return $out;
	}

}