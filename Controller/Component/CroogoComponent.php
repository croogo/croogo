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
 * Blocks for layout
 *
 * @var string
 * @access public
 */
	public $blocks_for_layout = array();

/**
 * Vocabularies for layout
 *
 * @var string
 * @access public
 */
	public $vocabularies_for_layout = array();

/**
 * Types for layout
 *
 * @var string
 * @access public
 */
	public $types_for_layout = array();

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
		$this->controller = $controller;

		if ($this->Session->check('Auth.User.id')) {
			$this->roleId = $this->Session->read('Auth.User.role_id');
		}

		if (!isset($this->controller->request->params['admin']) && !isset($this->controller->request->params['requested'])) {
			$this->blocks();
			$this->menus();
			$this->vocabularies();
			$this->types();
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

		// types
		$types = $this->controller->Node->Taxonomy->Vocabulary->Type->find('all', array(
			'conditions' => array(
				'OR' => array(
					'Type.plugin LIKE' => '',
					'Type.plugin' => null,
				),
			),
			'order' => 'Type.alias ASC',
		));
		$this->controller->set('types_for_admin_layout', $types);

		// vocabularies
		$vocabularies = $this->controller->Node->Taxonomy->Vocabulary->find('all', array(
			'recursive' => '-1',
			'conditions' => array(
				'OR' => array(
					'Vocabulary.plugin LIKE' => '',
					'Vocabulary.plugin' => null,
				),
			),
			'order' => 'Vocabulary.alias ASC',
		));
		$this->controller->set('vocabularies_for_admin_layout', $vocabularies);

		if (!Configure::read('Croogo.version')) {
			$this->controller->Setting->write('Croogo.version', file_get_contents(APP . 'VERSION.txt'));
		}
	}

/**
 * Blocks
 *
 * Blocks will be available in this variable in views: $blocks_for_layout
 *
 * @return void
 */
	public function blocks() {
		$regions = $this->controller->Block->Region->find('list', array(
			'conditions' => array(
				'Region.block_count >' => '0',
			),
			'fields' => array(
				'Region.id',
				'Region.alias',
			),
			'cache' => array(
				'name' => 'croogo_regions',
				'config' => 'croogo_blocks',
			),
		));
		foreach ($regions as $regionId => $regionAlias) {
			$this->blocks_for_layout[$regionAlias] = array();
			$findOptions = array(
				'conditions' => array(
					'Block.status' => 1,
					'Block.region_id' => $regionId,
					'AND' => array(
						array(
							'OR' => array(
								'Block.visibility_roles' => '',
								'Block.visibility_roles LIKE' => '%"' . $this->roleId . '"%',
							),
						),
						array(
							'OR' => array(
								'Block.visibility_paths' => '',
								'Block.visibility_paths LIKE' => '%"' . $this->controller->request->here . '"%',
								//'Block.visibility_paths LIKE' => '%"' . 'controller:' . $this->params['controller'] . '"%',
								//'Block.visibility_paths LIKE' => '%"' . 'controller:' . $this->params['controller'] . '/' . 'action:' . $this->params['action'] . '"%',
							),
						),
					),
				),
				'order' => array(
					'Block.weight' => 'ASC'
				),
				'cache' => array(
					'prefix' => 'croogo_blocks_' . $regionAlias . '_' . $this->roleId . '_',
					'config' => 'croogo_blocks',
				),
				'recursive' => '-1',
			);
			$blocks = $this->controller->Block->find('all', $findOptions);
			$this->processBlocksData($blocks);
			$this->blocks_for_layout[$regionAlias] = $blocks;
		}
	}

/**
 * Process blocks for bb-code like strings
 *
 * @param array $blocks
 * @return void
 */
	public function processBlocksData($blocks) {
		foreach ($blocks as $block) {
			$this->blocksData['menus'] = Set::merge($this->blocksData['menus'], $this->parseString('menu|m', $block['Block']['body']));
			$this->blocksData['vocabularies'] = Set::merge($this->blocksData['vocabularies'], $this->parseString('vocabulary|v', $block['Block']['body']));
			$this->blocksData['nodes'] = Set::merge($this->blocksData['nodes'], $this->parseString('node|n', $block['Block']['body'], array(
				'convertOptionsToArray' => true,
			)));
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
 * Vocabularies
 *
 * Vocabularies will be available in this variable in views: $vocabularies_for_layout
 *
 * @return void
 */
	public function vocabularies() {
		$vocabularies = array();
		$themeData = $this->getThemeData(Configure::read('Site.theme'));
		if (isset($themeData['vocabularies']) && is_array($themeData['vocabularies'])) {
			$vocabularies = Set::merge($vocabularies, $themeData['vocabularies']);
		}
		$vocabularies = Set::merge($vocabularies, array_keys($this->blocksData['vocabularies']));
		$vocabularies = array_unique($vocabularies);
		foreach ($vocabularies as $vocabularyAlias) {
			$vocabulary = $this->controller->Node->Taxonomy->Vocabulary->find('first', array(
				'conditions' => array(
					'Vocabulary.alias' => $vocabularyAlias,
				),
				'cache' => array(
					'name' => 'croogo_vocabulary_' . $vocabularyAlias,
					'config' => 'croogo_vocabularies',
				),
				'recursive' => '-1',
			));
			if (isset($vocabulary['Vocabulary']['id'])) {
				$threaded = $this->controller->Node->Taxonomy->find('threaded', array(
					'conditions' => array(
						'Taxonomy.vocabulary_id' => $vocabulary['Vocabulary']['id'],
					),
					'contain' => array(
						'Term',
					),
					'cache' => array(
						'name' => 'croogo_vocabulary_threaded_' . $vocabularyAlias,
						'config' => 'croogo_vocabularies',
					),
					'order' => 'Taxonomy.lft ASC',
				));
				$this->vocabularies_for_layout[$vocabularyAlias] = array();
				$this->vocabularies_for_layout[$vocabularyAlias]['Vocabulary'] = $vocabulary['Vocabulary'];
				$this->vocabularies_for_layout[$vocabularyAlias]['threaded'] = $threaded;
			}
		}
	}

/**
 * Types
 *
 * Types will be available in this variable in views: $types_for_layout
 *
 * @return void
 */
	public function types() {
		$types = $this->controller->Node->Taxonomy->Vocabulary->Type->find('all', array(
			'cache' => array(
				'name' => 'croogo_types',
				'config' => 'croogo_types',
			),
		));
		foreach ($types as $type) {
			$alias = $type['Type']['alias'];
			$this->types_for_layout[$alias] = $type;
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
		$nodes = $this->blocksData['nodes'];
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
 * Converts formatted string to array
 *
 * A string formatted like 'Node.type:blog;' will be converted to
 * array('Node.type' => 'blog');
 *
 * @param string $string in this format: Node.type:blog;Node.user_id:1;
 * @return array
 */
	public function stringToArray($string) {
		$string = explode(';', $string);
		$stringArr = array();
		foreach ($string as $stringElement) {
			if ($stringElement != null) {
				$stringElementE = explode(':', $stringElement);
				if (isset($stringElementE['1'])) {
					$stringArr[$stringElementE['0']] = $stringElementE['1'];
				} else {
					$stringArr[] = $stringElement;
				}
			}
		}

		return $stringArr;
	}

/**
 * beforeRender
 *
 * @param object $controller instance of controller
 * @return void
 */
	public function beforeRender(Controller $controller) {
		$this->controller = $controller;
		$this->controller->set('blocks_for_layout', $this->blocks_for_layout);
		$this->controller->set('menus_for_layout', $this->menus_for_layout);
		$this->controller->set('vocabularies_for_layout', $this->vocabularies_for_layout);
		$this->controller->set('types_for_layout', $this->types_for_layout);
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
 * Parses bb-code like string.
 *
 * Example: string containing [menu:main option1="value"] will return an array like
 *
 * Array
 * (
 *     [main] => Array
 *         (
 *             [option1] => value
 *         )
 * )
 *
 * @param string $exp
 * @param string $text
 * @param array  $options
 * @return array
 */
	public function parseString($exp, $text, $options = array()) {
		$_options = array(
			'convertOptionsToArray' => false,
		);
		$options = array_merge($_options, $options);

		$output = array();
		preg_match_all('/\[(' . $exp . '):([A-Za-z0-9_\-]*)(.*?)\]/i', $text, $tagMatches);
		for ($i = 0, $ii = count($tagMatches[1]); $i < $ii; $i++) {
			$regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
			preg_match_all($regex, $tagMatches[3][$i], $attributes);
			$alias = $tagMatches[2][$i];
			$aliasOptions = array();
			for ($j = 0, $jj = count($attributes[0]); $j < $jj; $j++) {
				$aliasOptions[$attributes[1][$j]] = $attributes[2][$j];
			}
			if ($options['convertOptionsToArray']) {
				foreach ($aliasOptions as $optionKey => $optionValue) {
					if (!is_array($optionValue) && strpos($optionValue, ':') !== false) {
						$aliasOptions[$optionKey] = $this->stringToArray($optionValue);
					}
				}
			}
			$output[$alias] = $aliasOptions;
		}
		return $output;
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
