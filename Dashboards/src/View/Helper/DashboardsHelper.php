<?php

namespace Croogo\Dashboards\View\Helper;
use Cake\View\Helper;
use Cake\Utility\Hash;
use Cake\Core\Configure;
use Cake\View\View;
use Croogo\Core\Croogo;
use Croogo\Dashboards\CroogoDashboard;
use Cake\ORM\TableRegistry;

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
class DashboardsHelper extends Helper {

	public $helpers = [
		'Croogo/Core.Layout',
		'Croogo/Core.CroogoHtml'
	];

/**
 * Constructor
 */
	public function __construct(View $View, $settings = array()) {
		$settings = Hash::merge(array(
			'dashboardTag' => 'div',
		), $settings);

		parent::__construct($View, $settings);
	}

/**
 * Before Render callback
 */
	public function beforeRender($viewFile) {
		if ($this->request->param('prefix') === 'admin') {
			Croogo::dispatchEvent('Croogo.setupAdminDashboardData', $this->_View);
		}
	}

/**
 * Gets the dashboard markup
 *
 * @return string
 */
	public function dashboards() {
		$registered = Configure::read('Dashboards');
		$userId = $this->_View->get('loggedInUser')['id'];
		if (empty($userId)) {
			return '';
		}

		$columns = array(
			CroogoDashboard::LEFT => array(),
			CroogoDashboard::RIGHT => array(),
			CroogoDashboard::FULL => array(),
		);
		if (empty($this->Roles)) {
			$this->Roles = TableRegistry::get('Croogo/Users.Roles');
			$this->Roles->addBehavior('Croogo/Core.Aliasable');
		}
		$currentRole = $this->Roles->byId($this->Layout->getRoleId());

		$cssSetting = $this->Layout->themeSetting('css');

		if (!empty($this->_View->viewVars['boxes_for_dashboard'])) {
			$boxesForLayout = collection($this->_View->viewVars['boxes_for_dashboard'])->combine('{n}.DashboardsDashboard.alias', '{n}.DashboardsDashboard')->toArray();
			$dashboards = array();
			$registeredUnsaved = array_diff_key($registered, $boxesForLayout);
			foreach ($boxesForLayout as $alias => $userBox) {
				if (isset($registered[$alias]) && $userBox['status']) {
					$dashboards[$alias] = array_merge($registered[$alias], $userBox);
				}
			}
			$dashboards = Hash::merge($dashboards, $registeredUnsaved);
			$dashboards = Hash::sort($dashboards, '{s}.weight', 'ASC');
		} else {
			$dashboards = Hash::sort($registered, '{s}.weight', 'ASC');
		}

		foreach ($dashboards as $alias => $dashboard) {
			if ($currentRole != 'admin' && !in_array($currentRole, $dashboard['access'])) {
				continue;
			}

			$opt = array(
				'alias' => $alias,
				'dashboard' => $dashboard,
			);
			Croogo::dispatchEvent('Croogo.beforeRenderDashboard', $this->_View, compact('alias', 'dashboard'));
			$dashboardBox = $this->_View->element('Croogo/Dashboards.admin/dashboard', $opt);
			Croogo::dispatchEvent('Croogo.afterRenderDashboard', $this->_View, compact('alias', 'dashboard', 'dashboardBox'));

			if ($dashboard['column'] === false) {
				$dashboard['column'] = count($columns[0]) <= count($columns[1]) ? CroogoDashboard::LEFT : CroogoDashboard::RIGHT;
			}

			$columns[$dashboard['column']][] = $dashboardBox;
		}

		$dashboardTag = $this->settings['dashboardTag'];
		$columnDivs = array(
			0 => $this->CroogoHtml->tag($dashboardTag, implode('', $columns[CroogoDashboard::LEFT]), array(
				'class' => $cssSetting['dashboardLeft'] . ' ' . $cssSetting['dashboardClass'],
				'id' => 'column-0',
			)),
			1 => $this->CroogoHtml->tag($dashboardTag, implode('', $columns[CroogoDashboard::RIGHT]), array(
				'class' => $cssSetting['dashboardRight'] . ' ' . $cssSetting['dashboardClass'],
				'id' => 'column-1'
			)),
		);
		$fullDiv = $this->CroogoHtml->tag($dashboardTag, implode('', $columns[CroogoDashboard::FULL]), array(
			'class' => $cssSetting['dashboardFull'] . ' ' . $cssSetting['dashboardClass'],
			'id' => 'column-2',
		));

		return $this->CroogoHtml->tag('div', $fullDiv, array('class' => $cssSetting['row'])) .
			$this->CroogoHtml->tag('div', implode('', $columnDivs), array('class' => $cssSetting['row']));
	}

/**
 * Gets a readable name from constants
 *
 * @param int $id CroogoDashboard position constants
 * @return string Readable position name
 */
	public function columnName($id) {
		switch ($id) {
			case CroogoDashboard::LEFT:
				return __d('croogo', 'Left');
			break;
			case CroogoDashboard::RIGHT:
				return __d('croogo', 'Right');
			break;
			case CroogoDashboard::FULL:
				return __d('croogo', 'Full');
			break;
		}
		return null;
	}

}
