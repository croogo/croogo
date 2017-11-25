<?php

namespace Croogo\Dashboards\View\Helper;

use Cake\View\Helper;
use Cake\Utility\Hash;
use Cake\Core\Configure;
use Cake\Log\Log;
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
class DashboardsHelper extends Helper
{

    public $helpers = [
        'Html' => ['className' => 'Croogo/Core.CroogoHtml'],
        'Croogo/Core.Layout',
        'Croogo/Core.Theme',
    ];

/**
 * Constructor
 */
    public function __construct(View $View, $settings = [])
    {
        $this->settings = Hash::merge([
            'dashboardTag' => 'div',
        ], $settings);

        parent::__construct($View, $settings);
    }

/**
 * Before Render callback
 */
    public function beforeRender($viewFile)
    {
        if ($this->request->param('prefix') === 'admin') {
            Croogo::dispatchEvent('Croogo.setupAdminDashboardData', $this->_View);
        }
    }

/**
 * Gets the dashboard markup
 *
 * @return string
 */
    public function dashboards()
    {
        $registered = Configure::read('Dashboards');
        $userId = $this->_View->get('loggedInUser')['id'];
        if (empty($userId)) {
            return '';
        }

        $columns = [
            CroogoDashboard::LEFT => [],
            CroogoDashboard::RIGHT => [],
            CroogoDashboard::FULL => [],
        ];
        if (empty($this->Roles)) {
            $this->Roles = TableRegistry::get('Croogo/Users.Roles');
            $this->Roles->addBehavior('Croogo/Core.Aliasable');
        }
        $currentRole = $this->Roles->byId($this->Layout->getRoleId());

        $cssSetting = $this->Theme->settings('css');

        if (!empty($this->_View->viewVars['boxes_for_dashboard'])) {
            $boxesForLayout = collection($this->_View->viewVars['boxes_for_dashboard'])->combine('alias', function($entity) {
                return $entity;
            })->toArray();
            $dashboards = [];
            $registeredUnsaved = array_diff_key($registered, $boxesForLayout);
            foreach ($boxesForLayout as $alias => $userBox) {
                if (isset($registered[$alias]) && $userBox['status']) {
                    $dashboards[$alias] = array_merge($registered[$alias], $userBox->toArray());
                }
            }
            $dashboards = Hash::merge($dashboards, $registeredUnsaved);
            $dashboards = Hash::sort($dashboards, '{s}.weight', 'ASC');
        } else {
            $dashboards = Hash::sort($registered, '{s}.weight', 'ASC');
        }

        foreach ($dashboards as $alias => $dashboard) {
            if ($currentRole != 'superadmin' &&
                (
                !empty($dashboard['access']) &&
                !in_array($currentRole, $dashboard['access'])
                )
            ) {
                continue;
            }

            if (empty($dashboard['cell'])) {
                Log::error('Dashboard ' . $alias . ' has no cell attribute');
                continue;
            }

            $opt = [
                'alias' => $alias,
                'dashboard' => $dashboard,
            ];
            Croogo::dispatchEvent('Croogo.beforeRenderDashboard', $this->_View, compact('alias', 'dashboard'));
            $dashboardBox = $this->_View->element('Croogo/Dashboards.admin/dashboard', $opt);
            Croogo::dispatchEvent('Croogo.afterRenderDashboard', $this->_View, compact('alias', 'dashboard', 'dashboardBox'));

            if ($dashboard['column'] === false) {
                $dashboard['column'] = count($columns[0]) <= count($columns[1]) ? CroogoDashboard::LEFT : CroogoDashboard::RIGHT;
            }

            $columns[$dashboard['column']][] = $dashboardBox;
        }

        $dashboardTag = $this->settings['dashboardTag'];
        $columnDivs = [
            0 => $this->Html->tag($dashboardTag, implode('', $columns[CroogoDashboard::LEFT]) . '&nbsp;', [
                'class' => $cssSetting['dashboardLeft'] . ' ' . $cssSetting['dashboardClass'],
                'id' => 'column-0',
            ]),
            1 => $this->Html->tag($dashboardTag, implode('', $columns[CroogoDashboard::RIGHT]) . '&nbsp;', [
                'class' => $cssSetting['dashboardRight'] . ' ' . $cssSetting['dashboardClass'],
                'id' => 'column-1'
            ]),
        ];
        $fullDiv = $this->Html->tag($dashboardTag, implode('', $columns[CroogoDashboard::FULL]) . '&nbsp;', [
            'class' => $cssSetting['dashboardFull'] . ' ' . $cssSetting['dashboardClass'],
            'id' => 'column-2',
        ]);

        return $this->Html->tag('div', $fullDiv, ['class' => $cssSetting['row']]) .
            $this->Html->tag('div', implode('', $columnDivs), ['class' => $cssSetting['row']]);
    }

/**
 * Gets a readable name from constants
 *
 * @param int $id CroogoDashboard position constants
 * @return string Readable position name
 */
    public function columnName($id)
    {
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
