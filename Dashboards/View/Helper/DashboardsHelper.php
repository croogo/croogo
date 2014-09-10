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
		'Html' => array('className' => 'Croogo.CroogoHtml'),
	);

/**
 * Before Render callback
 */
	public function beforeRender($viewFile) {
		if (isset($this->request->params['admin'])) {
			Croogo::dispatchEvent('Croogo.setupAdminDashboardData', $this->_View);
		}
	}

	public function dashboards($options = array()) {
		$dashboards = Configure::read('Dashboards');
		$userId = AuthComponent::user('id');
		if (empty($userId)) {
			return '';
		}

		$columns = array(
			0 => array(),
			1 => array(),
			2 => array()
		);
		$sorted = Hash::sort($dashboards, '{s}.weight', 'ASC');
		if (empty($this->Role)) {
			$this->Role = ClassRegistry::init('Users.Role');
			$this->Role->Behaviors->attach('Croogo.Aliasable');
		}
		$currentRole = $this->Role->byId($this->Layout->getRoleId());

		$cssSetting = $this->Layout->themeSetting('css');

		$index = 0;
		foreach ($sorted as $alias => $dashboard) {
			if ($currentRole != 'admin' && !in_array($currentRole, $dashboard['access'])) {
				continue;
			}

			$opt = array(
				'alias' => $alias,
				'dashboard' => $dashboard,
			);
			Croogo::dispatchEvent('Croogo.beforeRenderDashboard', $this->_View, compact('alias', 'dashboard'));
			$dashboardBox = $this->_View->element('Extensions.admin/dashboard', $opt);
			Croogo::dispatchEvent('Croogo.afterRenderDashboard', $this->_View, compact('alias', 'dashboard', 'dashboardBox'));

			$column = 2;
			if ($dashboard['full_width'] == false) {
				$column = $index % 2;
				$index++;
			}

			$columns[$column][] = $dashboardBox;
		}

		$columnDivs = array(
			0 => $this->Html->tag('div', implode('', $columns[0]), array('class' => $cssSetting['dashboardLeft'])),
			1 => $this->Html->tag('div', implode('', $columns[1]), array('class' => $cssSetting['dashboardRight']))
		);

		return $this->Html->tag('div', implode('', $columns[2]), array('class' => $cssSetting['row'])) .
				$this->Html->tag('div', implode('', $columnDivs), array('class' => $cssSetting['row']));
	}

}