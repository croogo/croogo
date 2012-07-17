<?php

class MenusComponent extends Component {

/**
 * Other components used by this component
 *
 * @var array
 * @access public
 */
	public $components = array(
		'Croogo',
		);

/**
 * Menus for layout
 *
 * @var string
 * @access public
 */
	public $menusForLayout = array();

/**
 * Startup
 *
 * @param object $controller instance of controller
 * @return void
 */
	public function startup(Controller $controller) {
		$this->controller = $controller;

		if (!isset($this->controller->request->params['admin']) && !isset($this->controller->request->params['requested'])) {
			$this->menus();

		} else {
			$this->_adminData();
		}
	}

	protected function _adminData() {
		// menus
		$menus = $this->controller->Link->Menu->find('all', array(
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
		$this->controller = $controller;
		$this->controller->set('menus_for_layout', $this->menusForLayout);
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
			$menus = Set::merge($menus, $themeData['menus']);
		}
		$menus = Set::merge($menus, array_keys($this->controller->Blocks->blocksData['menus']));

		foreach ($menus as $menuAlias) {
			$menu = $this->controller->Link->Menu->find('first', array(
				'conditions' => array(
					'Menu.status' => 1,
					'Menu.alias' => $menuAlias,
					'Menu.link_count >' => 0,
				),
				'cache' => array(
					'name' => 'croogo_menu_' . $menuAlias,
					'config' => 'croogo_menus',
				),
				'recursive' => '-1',
			));
			if (isset($menu['Menu']['id'])) {
				$this->menusForLayout[$menuAlias] = $menu;
				$findOptions = array(
					'conditions' => array(
						'Link.menu_id' => $menu['Menu']['id'],
						'Link.status' => 1,
						'AND' => array(
							array(
								'OR' => array(
									'Link.visibility_roles' => '',
									'Link.visibility_roles LIKE' => '%"' . $this->roleId . '"%',
								),
							),
						),
					),
					'order' => array(
						'Link.lft' => 'ASC',
					),
					'cache' => array(
						'name' => 'croogo_menu_' . $menu['Menu']['id'] . '_links_' . $this->roleId,
						'config' => 'croogo_menus',
					),
					'recursive' => -1,
				);
				$links = $this->controller->Link->find('threaded', $findOptions);
				$this->menusForLayout[$menuAlias]['threaded'] = $links;
			}
		}

	}

}
