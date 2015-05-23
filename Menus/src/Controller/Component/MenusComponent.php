<?php

namespace Croogo\Menus\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Croogo\Croogo\Controller\Component\CroogoComponent;

/**
 * Menus Component
 *
 * @property CroogoComponent Croogo
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
 * Startup
 *
 * @param object $controller instance of controller
 * @return void
 */
	public function startup(Event $event) {
		$this->controller = $event->subject();
		if (isset($this->controller->Link)) {
			$this->Links = $this->controller->Links;
		} else {
			$this->Links = TableRegistry::get('Croogo/Menus.Links');
		}

		$controller = $event->subject();
		if (!isset($controller->request->params['admin']) && !isset($controller->request->params['requested'])) {
			$this->menus();

		} else {
			$this->_adminData();
		}
	}

	protected function _adminData() {
		// menus
		$menus = $this->Links->Menus->find('all', array(
			'recursive' => '-1',
			'order' => 'Menu.id ASC',
		));
		$this->controller->set('menus_for_admin_layout', $menus);
	}

	/**
	 * beforeRender
	 *
	 * @param Event $event
	 */
	public function beforeRender(Event $event) {
		$event->subject()->set('menus_for_layout', $this->menusForLayout);
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
		$status = $this->Links->status();
		foreach ($menus as $menuAlias) {
			$menu = $this->Links->Menus->find('all', [
				'cache' => [
					'name' => $menuAlias,
					'config' => 'croogo_menus',
				],
			])->where(array(
				'Menus.status' => $status,
				'Menus.alias' => $menuAlias,
				'Menus.link_count >' => 0,
			))->first();
			if ($menu) {
				$this->menusForLayout[$menuAlias] = $menu;
				$links = $this->Links->find('threaded', [
					'cache' => array(
						'name' => $menu->alias . '_links_' . $roleId,
						'config' => 'croogo_menus',
					)
				])->where([
					'Links.menu_id' => $menu->id,
					'Links.status' => $status,
					'AND' => [
						[
							'OR' => [
								'Links.visibility_roles' => '',
								'Links.visibility_roles LIKE' => '%"' . $roleId . '"%',
							],
						],
					],
				])->order([
					'Links.lft' => 'ASC',
				]);
				$this->menusForLayout[$menuAlias]['threaded'] = $links;
			}
		}
	}

}
