<?php

App::uses('File', 'Utility');
App::uses('Folder', 'Utility');
App::uses('CroogoPlugin', 'Extensions.Lib');
App::uses('CroogoTheme', 'Extensions.Lib');

/**
 * Croogo Component
 *
 * PHP version 5
 *
 * @category Component
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoComponent extends Component {

/**
 * Other components used by this component
 *
 * @var array
 * @access public
 */
	public $components = array(
		'Session',
	);

/**
 * Role ID of current user
 *
 * Default is 3 (public)
 *
 * @var integer
 * @access public
 */
	public $roleId = 3;

/**
 * Menus for layout
 *
 * @var string
 * @access public
 */
	public $menus_for_layout = array();

/**
 * Nodes for layout
 *
 * @var string
 * @access public
 */
	public $nodes_for_layout = array();

/**
 * Blocks data: contains parsed value of bb-code like strings
 *
 * @var array
 * @access public
 */
	public $blocksData = array(
		'menus' => array(),
		'vocabularies' => array(),
		'nodes' => array(),
	);

/**
 * controller
 *
 * @var Controller
 */
	protected $controller = null;

/**
 * Method to lazy load classes
 *
 * @return Object
 */
	public function __get($name) {
		switch ($name) {
			case '_CroogoPlugin':
			case '_CroogoTheme':
				if (!isset($this->{$name})) {
					$class = substr($name, 1);
					$this->{$name} = new $class();
					if (method_exists($this->{$name}, 'setController')) {
						$this->{$name}->setController($this->controller);
					}
				}
				return $this->{$name};
			break;
			default:
				return parent::__get($name);
			break;
		}
	}

/**
 * Startup
 *
 * @param object $controller instance of controller
 * @return void
 */
	public function startup(Controller $controller) {
		$this->controller =& $controller;

		if ($this->Session->check('Auth.User.id')) {
			$this->roleId = $this->Session->read('Auth.User.role_id');
		}

		if (!isset($this->controller->request->params['admin']) && !isset($this->controller->request->params['requested'])) {
			$this->menus();
			$this->nodes();
		} else {
			$this->_adminData();
		}
	}

/**
 * Set variables for admin layout
 *
 * @return void
 */
	protected function _adminData() {
		// menus
		$menus = $this->controller->Link->Menu->find('all', array(
			'recursive' => '-1',
			'order' => 'Menu.id ASC',
		));
		$this->controller->set('menus_for_admin_layout', $menus);

		if (!Configure::read('Croogo.version')) {
			$this->controller->Setting->write('Croogo.version', file_get_contents(APP . 'VERSION.txt'));
		}
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
		$themeData = $this->getThemeData(Configure::read('Site.theme'));
		if (isset($themeData['menus']) && is_array($themeData['menus'])) {
			$menus = Set::merge($menus, $themeData['menus']);
		}
		$menus = Set::merge($menus, array_keys($this->blocksData['menus']));

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
				$this->menus_for_layout[$menuAlias] = $menu;
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
				$this->menus_for_layout[$menuAlias]['threaded'] = $links;
			}
		}
	}

/**
 * Nodes
 *
 * Nodes will be available in this variable in views: $nodes_for_layout
 *
 * @return void
 */
	public function nodes() {
		$nodes = $this->controller->Blocks->blocksData['nodes'];
		$_nodeOptions = array(
			'find' => 'all',
			'conditions' => array(
				'Node.status' => 1,
				'OR' => array(
					'Node.visibility_roles' => '',
					'Node.visibility_roles LIKE' => '%"' . $this->roleId . '"%',
				),
			),
			'order' => 'Node.created DESC',
			'limit' => 5,
		);

		foreach ($nodes as $alias => $options) {
			$options = Set::merge($_nodeOptions, $options);
			$options['limit'] = str_replace('"', '', $options['limit']);
			$node = $this->controller->Node->find($options['find'], array(
				'conditions' => $options['conditions'],
				'order' => $options['order'],
				'limit' => $options['limit'],
				'cache' => array(
					'prefix' => 'croogo_nodes_' . $alias . '_',
					'config' => 'croogo_nodes',
				),
			));
			$this->nodes_for_layout[$alias] = $node;
		}
	}

/**
 * beforeRender
 *
 * @param object $controller instance of controller
 * @return void
 */
	public function beforeRender(Controller $controller) {
		$this->controller =& $controller;
		$this->controller->set('menus_for_layout', $this->menus_for_layout);
		$this->controller->set('nodes_for_layout', $this->nodes_for_layout);
	}

/**
 * Extracts parameters from 'filter' named parameter.
 *
 * @return array
 */
	public function extractFilter() {
		$filter = explode(';', $this->controller->request->params['named']['filter']);
		$filterData = array();
		foreach ($filter as $f) {
			$fData = explode(':', $f);
			$fKey = $fData['0'];
			if ($fKey != null) {
				$filterData[$fKey] = $fData['1'];
			}
		}
		return $filterData;
	}

/**
 * Get URL relative to the app
 *
 * @param array $url
 * @return array
 */
	public function getRelativePath($url = '/') {
		if (is_array($url)) {
			$absoluteUrl = Router::url($url, true);
		} else {
			$absoluteUrl = Router::url('/' . $url, true);
		}
		$path = '/' . str_replace(Router::url('/', true), '', $absoluteUrl);
		return $path;
	}

/**
 * ACL: add ACO
 *
 * Creates ACOs with permissions for roles.
 *
 * @param string $action possible values: ControllerName, ControllerName/method_name
 * @param array $allowRoles Role aliases
 * @return void
 */
	public function addAco($action, $allowRoles = array()) {
		$this->controller->CroogoAccess->addAco($action, $allowRoles);
	}

/**
 * ACL: remove ACO
 *
 * Removes ACOs and their Permissions
 *
 * @param string $action possible values: ControllerName, ControllerName/method_name
 * @return void
 */
	public function removeAco($action) {
		$this->controller->CroogoAccess->removeAco($action);
	}

/**
 * Loads plugin's bootstrap.php file
 *
 * @param string $plugin Plugin name (underscored)
 * @return void
 * @deprecated use CroogoPlugin::addBootstrap()
 */
	public function addPluginBootstrap($plugin) {
		$this->_CroogoPlugin->addBootstrap($plugin);
	}

/**
 * Plugin name will be removed from Hook.bootstraps
 *
 * @param string $plugin Plugin name (underscored)
 * @return void
 * @deprecated use CroogoPlugin::removeBootstrap()
 */
	public function removePluginBootstrap($plugin) {
		$this->_CroogoPlugin->removeBootstrap($plugin);
	}

/**
 * Get theme aliases (folder names)
 *
 * @return array
 */
	public function getThemes() {
		return $this->_CroogoTheme->getThemes();
	}

/**
 * Get the content of theme.json file from a theme
 *
 * @param string $alias theme folder name
 * @return array
 * @deprecated use CroogoTheme::getData()
 */
	public function getThemeData($alias = null) {
		return $this->_CroogoTheme->getData($alias);
	}

/**
 * Get plugin alises (folder names)
 *
 * @return array
 * @deprecated use CroogoPlugin::getPlugins()
 */
	public function getPlugins() {
		return $this->_CroogoPlugin->getPlugins();
	}

/**
 * Get the content of plugin.json file of a plugin
 *
 * @param string $alias plugin folder name
 * @return array
 * @deprecated use CroogoPlugin::getData
 */
	public function getPluginData($alias = null) {
		return $this->_CroogoPlugin->getData($alias);
	}

/**
 * Check if plugin is dependent on any other plugin.
 * If yes, check if that plugin is available in plugins directory.
 *
 * @param  string $plugin plugin alias (underscrored)
 * @return boolean
 * @deprecated use CroogoPlugin::checkDependency()
 */
	public function checkPluginDependency($plugin = null) {
		return $this->_CroogoPlugin->checkDependency($plugin);
	}

/**
 * Check if plugin is active
 *
 * @param  string $plugin Plugin name (underscored)
 * @return boolean
 * @deprecated use CroogoPlugin::isActive
 */
	public function pluginIsActive($plugin) {
		return $this->_CroogoPlugin->isActive($plugin);
	}

}
