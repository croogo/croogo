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

	public function adminDashboard($options = array()) {
		$options = Hash::merge(array(
			'class' => 'span6',
		), $options);

		$dashboards = CroogoDashboard::items();
		$userId = AuthComponent::user('id');
		if (empty($userId)) {
			return '';
		}

		$columns = array(
			0 => array(),
			1 => array(),
			//Column '2' is the full width column
			2 => array()
		);
		$sorted = Hash::sort($dashboards, '{s}.weight', 'ASC');
		if (empty($this->Role)) {
			$this->Role = ClassRegistry::init('Users.Role');
			$this->Role->Behaviors->attach('Croogo.Aliasable');
		}
		$currentRole = $this->Role->byId($this->Layout->getRoleId());

		$index = 0;
		foreach ($sorted as $alias => $dashboard) {
			if ($currentRole != 'admin' && !in_array($currentRole, $dashboard['access'])) {
				continue;
			}

			$dashboardBox = $this->_View->element('Dashboard.admin/dashboard', array('alias' => $alias, 'dashboard' => $dashboard));

			$column = 2;
			if ($dashboard['full_width'] == false) {
				$column = $index % 2;
				$index++;
			}

			$columns[$column][] = $dashboardBox;
		}

		$columnDivs = array(
			0 => $this->Html->tag('div', implode('', $columns[0]), array('class' => $options['class'])),
			1 => $this->Html->tag('div', implode('', $columns[1]), array('class' => $options['class']))
		);

		return $this->Html->tag('div', implode('', $columns[2]), array('class' => 'row-fluid')) .
				$this->Html->tag('div', implode('', $columnDivs), array('class' => 'row-fluid'));
	}

}