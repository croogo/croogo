<?php
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
class CroogoComponent extends Object {
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
 * Startup
 *
 * @param object $controller instance of controller
 * @return void
 */
	public function startup(&$controller) {
		$this->controller =& $controller;
		App::import('Core', 'File');

		if ($this->Session->check('Auth.User.id')) {
			$this->roleId = $this->Session->read('Auth.User.role_id');
		}

		if (!isset($this->controller->params['admin']) && !isset($this->controller->params['requested'])) {
			$this->blocks();
			$this->menus();
			$this->vocabularies();
			$this->types();
			$this->nodes();
		} else {
			$this->__adminData();
		}
	}

/**
 * Set variables for admin layout
 *
 * @return void
 */
	private function __adminData() {
		// menus
		$menus = $this->controller->Link->Menu->find('all', array(
			'recursive' => '-1',
			'order' => 'Menu.id ASC',
		));
		$this->controller->set('menus_for_admin_layout', $menus);

		// types
		$types = $this->controller->Node->Taxonomy->Vocabulary->Type->find('all', array(
			'conditions' => array(
				'Type.plugin <>' => null,
			),
			'order' => 'Type.alias ASC',
		));
		$this->controller->set('types_for_admin_layout', $types);

		// vocabularies
		$vocabularies = $this->controller->Node->Taxonomy->Vocabulary->find('all', array(
			'recursive' => '-1',
			'conditions' => array(
				'Vocabulary.plugin <>' => null,
			),
			'order' => 'Vocabulary.alias ASC',
		));
		$this->controller->set('vocabularies_for_admin_layout', $vocabularies);
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
		foreach ($regions AS $regionId => $regionAlias) {
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
								'Block.visibility_paths LIKE' => '%"' . $this->controller->params['url']['url'] . '"%',
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
					'prefix' => 'croogo_blocks_'.$regionAlias.'_'.$this->roleId.'_',
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
		foreach ($blocks AS $block) {
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

		foreach ($menus AS $menuAlias) {
			$menu = $this->controller->Link->Menu->find('first', array(
				'conditions' => array(
					'Menu.status' => 1,
					'Menu.alias' => $menuAlias,
					'Menu.link_count >' => 0,
				),
				'cache' => array(
					'name' => 'croogo_menu_'.$menuAlias,
					'config' => 'croogo_menus',
				),
				'recursive' => '-1',
			));
			if (isset($menu['Menu']['id'])) {
				$this->menus_for_layout[$menuAlias] = array();
				$this->menus_for_layout[$menuAlias]['Menu'] = $menu['Menu'];
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
						'name' => 'croogo_menu_'.$menu['Menu']['id'].'_links_'.$this->roleId,
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
		foreach ($vocabularies AS $vocabularyAlias) {
			$vocabulary = $this->controller->Node->Taxonomy->Vocabulary->find('first', array(
				'conditions' => array(
					'Vocabulary.alias' => $vocabularyAlias,
				),
				'cache' => array(
					'name' => 'croogo_vocabulary_'.$vocabularyAlias,
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
						'name' => 'croogo_vocabulary_threaded_'.$vocabularyAlias,
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
		foreach ($types AS $type) {
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

		foreach ($nodes AS $alias => $options) {
			$options = Set::merge($_nodeOptions, $options);
			$options['limit'] = str_replace('"', '', $options['limit']);
			$node = $this->controller->Node->find($options['find'], array(
				'conditions' => $options['conditions'],
				'order' => $options['order'],
				'limit' => $options['limit'],
				'cache' => array(
					'prefix' => 'croogo_nodes_'.$alias.'_',
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
		foreach ($string AS $stringElement) {
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
	public function beforeRender(&$controller) {
		$this->controller =& $controller;
		$this->controller->set('blocks_for_layout', $this->blocks_for_layout);
		$this->controller->set('menus_for_layout', $this->menus_for_layout);
		$this->controller->set('vocabularies_for_layout', $this->vocabularies_for_layout);
		$this->controller->set('types_for_layout', $this->types_for_layout);
		$this->controller->set('nodes_for_layout', $this->nodes_for_layout);

		if ($controller->theme) {
			//$helperPaths = Configure::read('helperPaths');
			//array_unshift($helperPaths, APP.'views'.DS.'themed'.DS.$controller->theme.DS.'helpers'.DS);
			//Configure::write('helperPaths', $helperPaths);

			// Unless http://cakephp.lighthouseapp.com/projects/42648/tickets/84-enable-appbuild-to-retain-the-order-of-paths-given is integrated
			// with the main core, the following "proper" way won't work.
			//App::build(array('helpers' => array(APP . 'views' . DS . 'themed' . DS . $controller->theme . DS . 'helpers' . DS)));

			// Workaround
			$appInstance =& App::getInstance();
			array_unshift($appInstance->helpers, APP.'views'.DS.'themed'.DS.$controller->theme.DS.'helpers'.DS);
		}
	}

/**
 * Extracts parameters from 'filter' named parameter.
 *
 * @return array
 */
	public function extractFilter() {
		$filter = explode(';', $this->controller->params['named']['filter']);
		$filterData = array();
		foreach ($filter AS $f) {
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
		// AROs
		$aroIds = array();
		if (count($allowRoles) > 0) {
			$roles = ClassRegistry::init('Role')->find('list', array(
				'conditions' => array(
					'Role.alias' => $allowRoles,
				),
				'fields' => array(
					'Role.id',
					'Role.alias',
				),
			));
			$roleIds = array_keys($roles);
			$aros = $this->controller->Acl->Aro->find('list', array(
				'conditions' => array(
					'Aro.model' => 'Role',
					'Aro.foreign_key' => $roleIds,
				),
				'fields' => array(
					'Aro.id',
					'Aro.alias',
				),
			));
			$aroIds = array_keys($aros);
		}

		// ACOs
		$acoNode = $this->controller->Acl->Aco->node($this->controller->Auth->actionPath.$action);
		if (!isset($acoNode['0']['Aco']['id'])) {
			if (!strstr($action, '/')) {
				$parentNode = $this->controller->Acl->Aco->node(str_replace('/', '', $this->controller->Auth->actionPath));
				$alias = $action;
			} else {
				$actionE = explode('/', $action);
				$controllerName = $actionE['0'];
				$method = $actionE['1'];
				$alias = $method;
				$parentNode = $this->controller->Acl->Aco->node($this->controller->Auth->actionPath.$controllerName);
			}
			$parentId = $parentNode['0']['Aco']['id'];
			$acoData = array(
				'parent_id' => $parentId,
				'model' => null,
				'foreign_key' => null,
				'alias' => $alias,
			);
			$this->controller->Acl->Aco->id = false;
			$this->controller->Acl->Aco->save($acoData);
			$acoId = $this->controller->Acl->Aco->id;
		} else {
			$acoId = $acoNode['0']['Aco']['id'];
		}

		// Permissions (aros_acos)
		foreach ($aroIds AS $aroId) {
			$permission = $this->controller->Acl->Aro->Permission->find('first', array(
				'conditions' => array(
					'Permission.aro_id' => $aroId,
					'Permission.aco_id' => $acoId,
				),
			));
			if (!isset($permission['Permission']['id'])) {
				// create a new record
				$permissionData = array(
					'aro_id' => $aroId,
					'aco_id' => $acoId,
					'_create' => 1,
					'_read' => 1,
					'_update' => 1,
					'_delete' => 1,
				);
				$this->controller->Acl->Aco->Permission->id = false;
				$this->controller->Acl->Aco->Permission->save($permissionData);
			} else {
				// check if not permitted
				if ($permission['Permission']['_create'] == 0 ||
					$permission['Permission']['_read'] == 0 ||
					$permission['Permission']['_update'] == 0 ||
					$permission['Permission']['_delete'] == 0) {
					$permissionData = array(
						'id' => $permission['Permission']['id'],
						'aro_id' => $aroId,
						'aco_id' => $acoId,
						'_create' => 1,
						'_read' => 1,
						'_update' => 1,
						'_delete' => 1,
					);
					$this->controller->Acl->Aco->Permission->id = $permission['Permission']['id'];
					$this->controller->Acl->Aco->Permission->save($permissionData);
				}
			}
		}
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
		$acoNode = $this->controller->Acl->Aco->node($this->controller->Auth->actionPath.$action);
		if (isset($acoNode['0']['Aco']['id'])) {
			$this->controller->Acl->Aco->delete($acoNode['0']['Aco']['id']);
		}
	}

/**
 * Loads plugin's bootstrap.php file
 *
 * @param string $plugin Plugin name (underscored)
 * @return void
 */
	public function addPluginBootstrap($plugin) {
		$hookBootstraps = Configure::read('Hook.bootstraps');
		if (!$hookBootstraps) {
			$plugins = array();
		} else {
			$plugins = explode(',', $hookBootstraps);
		}

		if (array_search($plugin, $plugins) !== false) {
			$plugins = $hookBootstraps;
		} else {
			$plugins[] = $plugin;
			$plugins = implode(',', $plugins);
		}
		$this->controller->Setting->write('Hook.bootstraps', $plugins);
	}

/**
 * Plugin name will be removed from Hook.bootstraps
 *
 * @param string $plugin Plugin name (underscored)
 * @return void
 */
	public function removePluginBootstrap($plugin) {
		$hookBootstraps = Configure::read('Hook.bootstraps');
		if (!$hookBootstraps) {
			return;
		}

		$plugins = explode(',', $hookBootstraps);
		if (array_search($plugin, $plugins) !== false) {
			$k = array_search($plugin, $plugins);
			unset($plugins[$k]);
		}

		if (count($plugins) == 0) {
			$plugins = '';
		} else {
			$plugins = implode(',', $plugins);
		}
		$this->controller->Setting->write('Hook.bootstraps', $plugins);
	}

/**
 * Parses bb-code like string.
 *
 * Example: string containing [menu:main option1="value"] will return an array like
 *
 * Array
 * (
 *   [main] => Array
 *     (
 *       [option1] => value
 *     )
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
		preg_match_all('/\[('.$exp.'):([A-Za-z0-9_\-]*)(.*?)\]/i', $text, $tagMatches);
		for ($i=0; $i < count($tagMatches[1]); $i++) {
			$regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
			preg_match_all($regex, $tagMatches[3][$i], $attributes);
			$alias = $tagMatches[2][$i];
			$aliasOptions = array();
			for ($j=0; $j < count($attributes[0]); $j++) {
				$aliasOptions[$attributes[1][$j]] = $attributes[2][$j];
			}
			if ($options['convertOptionsToArray']) {
				foreach ($aliasOptions AS $optionKey => $optionValue) {
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
 * Get theme alises (folder names)
 *
 * @return array
 */
	public function getThemes() {
		$themes = array(
			'default' => 'default',
		);
		$this->folder = new Folder;
		$viewPaths = App::path('views');
		foreach ($viewPaths AS $viewPath) {
			$this->folder->path = $viewPath . 'themed';
			$themeFolders = $this->folder->read();
			foreach ($themeFolders['0'] AS $themeFolder) {
				$this->folder->path = $viewPath . 'themed' . DS . $themeFolder . DS . 'webroot';
				$themeFolderContent = $this->folder->read();
				if (in_array('theme.yml', $themeFolderContent['1'])) {
					$themes[$themeFolder] = $themeFolder;
				}
			}
		}
		return $themes;
	}

/**
 * Get the content of theme.yml file
 *
 * @param string $alias theme folder name
 * @return array
 */
	public function getThemeData($alias = null) {
		if ($alias == null || $alias == 'default') {
			$ymlLocation = WWW_ROOT . 'theme.yml';
		} else {
			$viewPaths = App::path('views');
			foreach ($viewPaths AS $viewPath) {
				if (file_exists($viewPath . 'themed' . DS . $alias . DS . 'webroot' . DS . 'theme.yml')) {
					$ymlLocation = $viewPath . 'themed' . DS . $alias . DS . 'webroot' . DS . 'theme.yml';
					continue;
				}
			}
			if (!isset($ymlLocation)) {
				$ymlLocation = WWW_ROOT . 'theme.yml';
			}
		}
		$themeData = Spyc::YAMLLoad(file_get_contents($ymlLocation));
		return $themeData;
	}

/**
 * Get plugin alises (folder names)
 *
 * @return array
 */
	public function getPlugins() {
		$plugins = array();
		$this->folder = new Folder;
		$pluginPaths = App::path('plugins');
		foreach ($pluginPaths AS $pluginPath) {
			$this->folder->path = $pluginPath;
			$pluginFolders = $this->folder->read();
			foreach ($pluginFolders[0] AS $pluginFolder) {
				if (substr($pluginFolder, 0, 1) != '.') {
					$this->folder->path = $pluginPath . $pluginFolder . DS . 'config';
					$pluginFolderContent = $this->folder->read();
					if (in_array('plugin.yml', $pluginFolderContent[1])) {
						$plugins[$pluginFolder] = $pluginFolder;
					}
				}
			}
		}
		return $plugins;
	}

/**
 * Get the content of plugin.yml file
 *
 * @param string $alias plugin folder name
 * @return array
 */
	public function getPluginData($alias = null) {
		$pluginPaths = App::path('plugins');
		foreach ($pluginPaths AS $pluginPath) {
			$ymlLocation = $pluginPath . $alias . DS . 'config' . DS . 'plugin.yml';
			if (file_exists($ymlLocation)) {
				$pluginData = Spyc::YAMLLoad(file_get_contents($ymlLocation));
				$pluginData['active'] = $this->pluginIsActive($alias);
				return $pluginData;
			}
		}
		return false;
	}

/**
 * Check if plugin is dependent on any other plugin.
 * If yes, check if that plugin is available in plugins directory.
 *
 * @param  string $plugin plugin alias (underscrored)
 * @return boolean
 */
	public function checkPluginDependency($plugin = null) {
		$pluginData = $this->getPluginData($plugin);
		$pluginPaths = App::path('plugins');
		if (isset($pluginData['dependencies']['plugins']) && is_array($pluginData['dependencies']['plugins'])) {
			foreach ($pluginData['dependencies']['plugins'] AS $p) {
				$check = false;
				foreach ($pluginPaths AS $pluginPath) {
					if (is_dir($pluginPath . $p)) {
						$check = true;
					}
				}
				if (!$check) {
					return false;
				}
			}
		}
		return true;
	}

/**
 * Check if plugin is active
 *
 * @param  string $plugin Plugin name (underscored)
 * @return boolean
 */
	public function pluginIsActive($plugin) {
		$configureKeys = array(
			'Hook.bootstraps',
		);

		foreach ($configureKeys AS $configureKey) {
			$hooks = explode(',', Configure::read($configureKey));
			foreach ($hooks AS $hook) {
				if ($hook == $plugin) {
					return true;
				}
			}
		}

		return false;
	}

}
