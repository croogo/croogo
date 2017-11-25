<?php

namespace Croogo\Menus\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Croogo\Core\Controller\Component\CroogoComponent;
use Croogo\Extensions\CroogoTheme;

/**
 * Menus Component
 *
 * @property CroogoComponent Croogo
 * @package Croogo.Menus.Controller.Component
 */
class MenuComponent extends Component
{

/**
 * Other components used by this component
 *
 * @var array
 * @access public
 */
    public $components = [
        'Croogo.Croogo',
    ];

/**
 * Menus for layout
 *
 * @var string
 * @access public
 */
    public $menusForLayout = [];

/**
 * Startup
 *
 * @param object $controller instance of controller
 * @return void
 */
    public function startup(Event $event)
    {
        $this->controller = $event->subject();
        if (isset($this->controller->Link)) {
            $this->Links = $this->controller->Links;
        } else {
            $this->Links = TableRegistry::get('Croogo/Menus.Links');
        }

        $controller = $event->subject();
        if (($controller->request->param('prefix') !== 'admin') && !isset($controller->request->params['requested'])) {
            $this->menus();

        } else {
            $this->_adminData();
        }
    }

    protected function _adminData()
    {
        // menus
        $menus = $this->Links->Menus
            ->find('all')
            ->order([
                $this->Links->Menus->aliasField('id') => 'ASC',
            ]);
        $this->controller->set('menus_for_admin_layout', $menus);
    }

    /**
     * beforeRender
     *
     * @param Event $event
     */
    public function beforeRender(Event $event)
    {
        $event->subject()->set('menusForLayout', $this->menusForLayout);
    }

/**
 * Menus
 *
 * Menus will be available in this variable in views: $menusForLayout
 *
 * @return void
 */
    public function menus()
    {
        $menus = ['main'];

        if (Configure::read('Site.theme')) {
            $themeData = (new CroogoTheme)->getData(Configure::read('Site.theme'));
            if (isset($themeData['menus']) && is_array($themeData['menus'])) {
                $menus = Hash::merge($menus, $themeData['menus']);
            }
        }

        $menus = Hash::merge($menus, array_keys($this->controller->BlocksHook->blocksData['menus']));

        $roleId = $this->controller->Croogo->roleId();
        $status = $this->Links->status();
        foreach ($menus as $menuAlias) {
            $menu = $this->Links->Menus->find('all', [
                'cache' => [
                    'name' => $menuAlias,
                    'config' => 'croogo_menus',
                ],
            ])->where([
                'Menus.status IN' => $status,
                'Menus.alias' => $menuAlias,
                'Menus.link_count >' => 0,
            ])->first();
            if ($menu) {
                $this->menusForLayout[$menuAlias] = $menu;
                $links = $this->Links->find('threaded', [
                    'cache' => [
                        'name' => $menu->alias . '_links_' . $roleId,
                        'config' => 'croogo_menus',
                    ]
                ])->find('byAccess', ['roleId' => $roleId])->where([
                    'Links.menu_id' => $menu->id,
                    'Links.status IN' => $status,
                ])->order([
                    'Links.lft' => 'ASC',
                ]);
                $this->menusForLayout[$menuAlias]['threaded'] = $links;
            }
        }
    }
}
