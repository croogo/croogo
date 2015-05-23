<?php

namespace Croogo\Menus\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Menus Component
 *
 * @package Croogo.Menus.Controller.Component
 */
class MenusComponent extends Component {

/**
 * Other components used by this component
 *
 * @var array
 * @access public
 */
	public $components = array(
		'Croogo.Croogo',
	);

/**
 * Menus for layout
 *
 * @var string
 * @access public
 */
	public $menusForLayout = array();

/**
 * initialize
 *
 * @param Controller $controller instance of controller
 */
	public function initialize(Event $event) {
		$this->controller = $event->subject();
		if (isset($controller->Link)) {
			$this->Link = $controller->Link;
		} else {
			$this->Link = TableRegistry::get('Menus.Link');
		}
	}

/**
 * Startup
 *
 * @param object $controller instance of controller
 * @return void
 */
	public function startup(Event $event) {
		$controller = $event->subject();
		if (!isset($controller->request->params['admin']) && !isset($controller->request->params['requested'])) {
			$this->menus();

		} else {
			$this->_adminData();
		}
	}

	protected function _adminData() {
		// menus
		$menus = $this->Link->Menu->find('all', array(
			'recursive' => '-1',
			'order' => 'Menu.id ASC',
		));
		$this->controller->set('menus_for_admin_layout', $menus);
	}

/**
 * beforeRender
 *
 * @param object $controller instance of controller
 * @return void
 */
	public function beforeRender(Controller $controller) {
		$controller->set('menus_for_layout', $this->menusForLayout);
	}

/**
 * Menus
 *
 * Menus will be available in this variable in views: $menus_for_layout
 *
 * @return void
 */
	public function menus() {
		$menus = array();
		$themeData = $this->Croogo->getThemeData(Configure::read('Site.theme'));
		if (isset($themeData['menus']) && is_array($themeData['menus'])) {
			$menus = Hash::merge($menus, $themeData['menus']);
		}
		$menus = Hash::merge($menus, array_keys($this->controller->Blocks->blocksData['menus']));

		$roleId = $this->controller->Croogo->roleId();
		$status = $this->Link->status();
		foreach ($menus as $menuAlias) {
			$menu = $this->Link->Menu->find('first', array(
				'conditions' => array(
					'Menu.status' => $status,
					'Menu.alias' => $menuAlias,
					'Menu.link_count >' => 0,
				),
				'cache' => array(
					'name' => $menuAlias,
					'config' => 'croogo_menus',
				),
				'recursive' => '-1',
			));
			if (isset($menu['Menu']['id'])) {
				$this->menusForLayout[$menuAlias] = $menu;
				$findOptions = array(
					'conditions' => array(
						'Link.menu_id' => $menu['Menu']['id'],
						'Link.status' => $status,
						'AND' => array(
							array(
								'OR' => array(
									'Link.visibility_roles' => '',
									'Link.visibility_roles LIKE' => '%"' . $roleId . '"%',
								),
							),
						),
					),
					'order' => array(
						'Link.lft' => 'ASC',
					),
					'cache' => array(
						'name' => $menu['Menu']['alias'] . '_links_' . $roleId,
						'config' => 'croogo_menus',
					),
					'recursive' => -1,
				);
				$links = $this->Link->find('threaded', $findOptions);
				$this->menusForLayout[$menuAlias]['threaded'] = $links;
			}
		}
	}

}
