<?php
App::uses('AppHelper', 'View/Helper');

/**
 * Croogo Helper
 *
 * @category Helper
 * @package  Croogo.Dashboard.View.Helper
 * @version  2.2
 * @author   Walther Lalk <emailme@waltherlalk.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class DashboardHelper extends AppHelper {

	public $helpers = array(
		'Croogo.Layout',
	);

/**
 * Before Render callback
 */
	public function beforeRender($viewFile) {
		if (isset($this->request->params['admin'])) {
			Croogo::dispatchEvent('Croogo.setupAdminDashboardData', $this->_View);
		}
	}

	public function adminDashboard($options = array()) {
		$options = Hash::merge(array(
			'class' => 'span4',
		), $options);

		$dashboards = CroogoDashboard::items();
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

		foreach ($sorted as $dashboard) {
			if ($currentRole != 'admin' && !in_array($currentRole, $dashboard['access'])) {
				continue;
			}

			$out .= $this->_View->element('Extensions.admin/dashboard', array('dashboard' => $dashboard, 'class' => $options['class']));
		}

		return $out;
	}

}