<?php
App::uses('CroogoEventManager', 'Croogo.Event');
App::uses('ClassRegistry', 'Utility');
App::uses('Folder', 'Utility');
App::uses('Hash', 'Utility');
App::uses('MigrationVersion', 'Migrations.Lib');

/**
 * CroogoPlugin utility class
 *
 * PHP version 5
 *
 * @category Component
 * @package  Croogo.Extensions.Lib
 * @version  1.4
 * @since    1.4
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoPlugin extends Object {

/**
 * List of migration errors
 * Updated in case of errors when running migrations
 *
 * @var array
 */
	public $migrationErrors = array();

/**
 * PluginActivation class
 *
 * @var object
 */
	protected $_PluginActivation = null;

/**
 * MigrationVersion class
 *
 * @var MigrationVersion
 */
	protected $_MigrationVersion = null;

/**
 * Core plugins
 *
 * Typically these plugins must be active and should not be deactivated
 *
 * @var array
 * @access public
 */
	public $corePlugins = array(
		'Acl',
		'Croogo',
		'Extensions',
		'Migrations',
		'Search',
		'Settings',
	);

/**
 * Bundled plugins providing core functionalities but could be deactivated
 *
 * @var array
 * @access public
 */
	public $bundledPlugins = array(
		'Blocks',
		'Comments',
		'Contacts',
		'FileManager',
		'Meta',
		'Menus',
		'Nodes',
		'Taxonomy',
		'Users',
	);

/**
 * __construct
 */
	public function __construct($migrationVersion = null) {
		$this->Setting = ClassRegistry::init('Settings.Setting');

		if (!is_null($migrationVersion)) {
			$this->_MigrationVersion = $migrationVersion;
		}
	}

/**
 * AppController setter
 *
 * @return void
 */
	public function setController(AppController $controller) {
		$this->_Controller = $controller;
	}

/**
 * Get plugin aliases (folder names)
 *
 * @return array
 */
	public function getPlugins() {
		$plugins = array();
		$this->folder = new Folder;
		$pluginPaths = App::path('plugins');
		foreach ($pluginPaths as $pluginPath) {
			$this->folder->path = $pluginPath;
			if (!file_exists($this->folder->path)) {
				continue;
			}
			$pluginFolders = $this->folder->read();
			foreach ($pluginFolders[0] as $pluginFolder) {
				if (substr($pluginFolder, 0, 1) != '.') {
					if (!$this->_isCroogoPlugin($pluginPath, $pluginFolder)) {
						continue;
					}
					$plugins[$pluginFolder] = $pluginFolder;
				}
			}
		}
		return $plugins;
	}

/**
 * Checks wether $pluginDir/$path is a Croogo plugin
 *
 * @param string $pluginDir plugin directory
 * @param string $path plugin alias
 * @return bool true if path is a Croogo plugin
 */
	protected function _isCroogoPlugin($pluginDir, $path) {
		$dir = $pluginDir . $path . DS;
		if (file_exists($dir . 'Config' . DS . 'plugin.json')) {
			return true;
		}
		return false;
	}

/**
 * Checks whether $plugin is builtin
 *
 * @param string $plugin plugin alias
 * @return boolean true if $plugin is builtin
 */
	protected function _isBuiltin($plugin) {
		return
			in_array($plugin, $this->bundledPlugins) ||
			in_array($plugin, $this->corePlugins);
	}

/**
 * Get the content of plugin.json file of a plugin
 *
 * @param string $alias plugin folder name
 * @return array|bool array of plugin manifest or boolean false
 */
	public function getData($alias = null) {
		$pluginPaths = App::path('plugins');
		foreach ($pluginPaths as $pluginPath) {
			$manifestFile = $pluginPath . $alias . DS . 'Config' . DS . 'plugin.json';
			$hasManifest = file_exists($manifestFile);
			if ($hasManifest) {
				$pluginData = json_decode(file_get_contents($manifestFile), true);
				if (!empty($pluginData)) {
					$pluginData['active'] = $this->isActive($alias);
					$pluginData['needMigration'] = $this->needMigration($alias, $pluginData['active']);
				} else {
					$this->log('plugin.json exists but cannot be decoded.');
					$pluginData = array();
				}
				return $pluginData;
			} elseif (in_array($alias, $this->bundledPlugins)) {
				if ($this->needMigration($alias, true)) {
					$pluginData = array(
						'name' => $alias,
						'description' => "Croogo $alias plugin",
						'active' => true,
						'needMigration' => true,
					);
					return $pluginData;
				}
			}
		}
		return false;
	}

/**
 * Get the content of plugin.json file of a plugin
 *
 * @param string $alias plugin folder name
 * @return array
 * @deprecated use getData()
 */
	public function getPluginData($alias = null) {
		return $this->getData($alias);
	}

/**
 * Get a list of plugins available with all available meta data.
 * Plugin without metadata are excluded.
 *
 * @return array array of plugins, listed according to bootstrap order
 */
	public function plugins() {
		$pluginAliases = $this->getPlugins();
		$allPlugins = array();
		foreach ($pluginAliases as $pluginAlias) {
			$allPlugins[$pluginAlias] = $this->getData($pluginAlias);
		}

		$activePlugins = array();
		$bootstraps = explode(',', Configure::read('Hook.bootstraps'));
		foreach ($bootstraps as $pluginAlias) {
			if ($pluginData = $this->getData($pluginAlias)) {
				$activePlugins[$pluginAlias] = $pluginData;
			}
		}

		$plugins = array();
		foreach ($activePlugins as $plugin => $pluginData) {
			$plugins[$plugin] = $pluginData;
		}
		$plugins = Hash::merge($plugins, $allPlugins);
		return $plugins;
	}

/**
 * Check if plugin is dependent on any other plugin.
 * If yes, check if that plugin is available in plugins directory.
 *
 * @param  string $plugin plugin alias
 * @return boolean
 */
	public function checkDependency($plugin = null) {
		$dependencies = $this->getDependencies($plugin);
		$pluginPaths = App::path('plugins');
		foreach ($dependencies as $p) {
			$check = false;
			foreach ($pluginPaths as $pluginPath) {
				if (is_dir($pluginPath . $p)) {
					$check = true;
				}
			}
			if (!$check) {
				return false;
			}
		}
		return true;
	}

/**
 * getDependencies
 *
 * @param  string $plugin plugin alias (underscrored)
 * @return array list of plugin that $plugin depends on
 */
	public function getDependencies($plugin) {
		$pluginData = $this->getData($plugin);
		if (!isset($pluginData['dependencies']['plugins'])) {
			$pluginData['dependencies']['plugins'] = array();
		}
		$dependencies = array();
		foreach ($pluginData['dependencies']['plugins'] as $i => $plugin) {
			$dependencies[] = Inflector::camelize($plugin);
		}
		return $dependencies;
	}

/**
 * Check if plugin is dependent on any other plugin.
 * If yes, check if that plugin is available in plugins directory.
 *
 * @param  string $plugin plugin alias (underscrored)
 * @return boolean
 */
	public function checkPluginDependency($plugin = null) {
		return $this->checkDependency($plugin);
	}

/**
 * Check if plugin is active
 *
 * @param  string $plugin Plugin name (underscored)
 * @return boolean
 */
	public function isActive($plugin) {
		$configureKeys = array(
			'Hook.bootstraps',
		);

		$plugin = array(Inflector::underscore($plugin), Inflector::camelize($plugin));

		foreach ($configureKeys as $configureKey) {
			$hooks = explode(',', Configure::read($configureKey));
			foreach ($hooks as $hook) {
				if (in_array($hook, $plugin)) {
					return true;
				}
			}
		}

		return false;
	}

/**
 * Check if plugin is active
 *
 * @param  string $plugin Plugin name (underscored)
 * @return boolean
 * @deprecated use isActive()
 */
	public function pluginIsActive($plugin) {
		return $this->isActive($plugin);
	}

/**
 * Check if a plugin need a database migration
 *
 * @param string $plugin Plugin name
 * @param string $isActive If the plugin is active
 * @return boolean
 */
	public function needMigration($plugin, $isActive) {
		$needMigration = false;
		if ($isActive) {
			$mapping = $this->_getMigrationVersion()->getMapping($plugin);
			$currentVersion = $this->_getMigrationVersion()->getVersion($plugin);
			if ($mapping) {
				$lastVersion = max(array_keys($mapping));
				$needMigration = ($lastVersion - $currentVersion != 0);
			}
		}
		return $needMigration;
	}

/**
 * Migrate a plugin
 *
 * @param string $plugin Plugin name
 * @return boolean Success of the migration
 */
	public function migrate($plugin) {
		$success = false;
		$mapping = $this->_getMigrationVersion()->getMapping($plugin);
		if ($mapping) {
			$lastVersion = max(array_keys($mapping));
			$executionResult = $this->_MigrationVersion->run(array(
				'version' => $lastVersion,
				'type' => $plugin
			));

			$success = $executionResult === true;
			if (!$success) {
				array_push($this->migrationErrors, $executionResult);
			}
		}
		return $success;
	}

	public function unmigrate($plugin) {
		$success = false;
		if ($this->_getMigrationVersion()->getMapping($plugin)) {
			$success = $this->_getMigrationVersion()->run(array(
				'version' => 0,
				'type' => $plugin,
				'direction' => 'down'
			));
		}
		return $success;
	}

	protected function _getMigrationVersion() {
		if (!($this->_MigrationVersion instanceof MigrationVersion)) {
			$this->_MigrationVersion = new MigrationVersion();
		}
		return $this->_MigrationVersion;
	}

/**
 * Loads plugin's bootstrap.php file
 *
 * @param string $plugin Plugin name
 * @return void
 */
	public function addBootstrap($plugin) {
		$hookBootstraps = Configure::read('Hook.bootstraps');
		if (!$hookBootstraps) {
			$plugins = array();
		} else {
			$plugins = explode(',', $hookBootstraps);
			$names = array(Inflector::underscore($plugin), Inflector::camelize($plugin));
			if ($intersect = array_intersect($names, $plugins)) {
				$plugin = current($intersect);
			}
		}

		if (array_search($plugin, $plugins) !== false) {
			$plugins = (array)$hookBootstraps;
		} else {
			$plugins[] = $plugin;
		}
		$this->_saveBootstraps($plugins);
	}

/**
 * Loads plugin's bootstrap.php file
 *
 * @param string $plugin Plugin name
 * @return void
 * @deprecated use addBootstrap($plugin)
 */
	public function addPluginBootstrap($plugin) {
		$this->addBootstrap($plugin);
	}

/**
 * Plugin name will be removed from Hook.bootstraps
 *
 * @param string $plugin Plugin name
 * @return void
 */
	public function removeBootstrap($plugin) {
		$hookBootstraps = Configure::read('Hook.bootstraps');
		if (!$hookBootstraps) {
			return;
		}

		$plugins = explode(',', $hookBootstraps);
		$names = array(Inflector::underscore($plugin), Inflector::camelize($plugin));
		if ($intersect = array_intersect($names, $plugins)) {
			$plugin = current($intersect);
			$k = array_search($plugin, $plugins);
			unset($plugins[$k]);
		}

		$this->_saveBootstraps($plugins);
	}

/**
 * Plugin name will be removed from Hook.bootstraps
 *
 * @param string $plugin Plugin name
 * @return void
 * @deprecated use removeBootstrap()
 */
	public function removePluginBootstrap($plugin) {
		$this->removeBootstrap($plugin);
	}

/**
 * Get PluginActivation class
 *
 * @param string $plugin
 * @return object
 */
	public function getActivator($plugin = null) {
		$plugin = Inflector::camelize($plugin);
		if (!isset($this->_PluginActivation)) {
			$className = $plugin . 'Activation';
			$configFile = APP . 'Plugin' . DS . $plugin . DS . 'Config' . DS . $className . '.php';
			if (file_exists($configFile) && include $configFile) {
				$this->_PluginActivation = new $className;
			}
		}
		return $this->_PluginActivation;
	}

/**
 * Activate plugin
 *
 * @param string $plugin Plugin name
 * @return boolean true when successful, false or error message when failed
 */
	public function activate($plugin) {
		if (CakePlugin::loaded($plugin)) {
			return __d('croogo', 'Plugin "%s" is already active.', $plugin);
		}
		$pluginActivation = $this->getActivator($plugin);
		if (!isset($pluginActivation) ||
			(isset($pluginActivation) && method_exists($pluginActivation, 'beforeActivation') && $pluginActivation->beforeActivation($this->_Controller))) {
			$pluginData = $this->getData($plugin);
			$dependencies = true;
			if (!empty($pluginData['dependencies']['plugins'])) {
				foreach ($pluginData['dependencies']['plugins'] as $requiredPlugin) {
					$requiredPlugin = ucfirst($requiredPlugin);
					if (!CakePlugin::loaded($requiredPlugin)) {
						$dependencies = false;
						$missingPlugin = $requiredPlugin;
						break;
					}
				}
			}
			if ($dependencies) {
				$this->addBootstrap($plugin);
				CroogoPlugin::load($plugin);
				if (isset($pluginActivation) && method_exists($pluginActivation, 'onActivation')) {
					$pluginActivation->onActivation($this->_Controller);
				}
				Cache::delete('EventHandlers', 'cached_settings');
				return true;
			} else {
				return __d('croogo', 'Plugin "%s" depends on "%s" plugin.', $plugin, $missingPlugin);
			}
			return __d('croogo', 'Plugin "%s" could not be activated. Please, try again.', $plugin);
		}
	}

/**
 * Deactivate plugin
 *
 * @param string $plugin Plugin name
 * @return boolean true when successful, false or error message when failed
 */
	public function deactivate($plugin) {
		if (!CakePlugin::loaded($plugin)) {
			return __d('croogo', 'Plugin "%s" is not active.', $plugin);
		}
		$pluginActivation = $this->getActivator($plugin);
		if (!isset($pluginActivation) ||
			(isset($pluginActivation) && method_exists($pluginActivation, 'beforeDeactivation') && $pluginActivation->beforeDeactivation($this->_Controller))) {
			$this->removeBootstrap($plugin);
			if (isset($pluginActivation) && method_exists($pluginActivation, 'onDeactivation')) {
				$pluginActivation->onDeactivation($this->_Controller);
			}
			CroogoPlugin::unload($plugin);
			Cache::delete('EventHandlers', 'cached_settings');
			return true;
		} else {
			return __d('croogo', 'Plugin could not be deactivated. Please, try again.');
		}
	}

/**
 * Loads a plugin and optionally loads bootstrapping and routing files.
 *
 * This method is identical to CakePlugin::load() with extra functionality
 * that loads event configuration when Plugin/Config/events.php is present.
 *
 * @see CakePlugin::load()
 * @param mixed $plugin name of plugin, or array of plugin and its config
 * @return void
 */
	public static function load($plugin, $config = array()) {
		CakePlugin::load($plugin, $config);
		if (is_string($plugin)) {
			$plugin = array($plugin => $config);
		}
		foreach ($plugin as $name => $conf) {
			list($name, $conf) = (is_numeric($name)) ? array($conf, $config) : array($name, $conf);
			$file = CakePlugin::path($name) . 'Config' . DS . 'events.php';
			if (file_exists($file)) {
				Configure::load($name . '.events');
			}
		}
	}

/**
 * Forgets a loaded plugin or all of them if first parameter is null
 *
 * This method is identical to CakePlugin::load() with extra functionality
 * that unregister event listeners when a plugin in unloaded.
 *
 * @see CakePlugin::unload()
 * @param string $plugin name of the plugin to forget
 * @return void
 */
	public static function unload($plugin) {
		$eventManager = CroogoEventManager::instance();
		if ($eventManager instanceof CroogoEventManager) {
			if ($plugin == null) {
				$activePlugins = CakePlugin::loaded();
				foreach ($activePlugins as $activePlugin) {
					$eventManager->detachPluginSubscribers($activePlugin);
				}
			} else {
				$eventManager->detachPluginSubscribers($plugin);
			}
		}
		CakePlugin::unload($plugin);
	}

/**
 * Delete plugin
 *
 * @param string $plugin Plugin name
 * @return boolean true when successful, false or array of error messages when failed
 * @throws InvalidArgumentException
 */
	public function delete($plugin) {
		if (empty($plugin)) {
			throw new InvalidArgumentException(__d('croogo', 'Invalid plugin'));
		}
		$pluginPath = APP . 'Plugin' . DS . $plugin;
		if (is_link($pluginPath)) {
			return unlink($pluginPath);
		}
		$folder = new Folder();
		$result = $folder->delete($pluginPath);
		if ($result !== true) {
			return $folder->errors();
		}
		return true;
	}

/**
 * Move plugin up or down in the bootstrap order
 *
 * @param string $dir valid values 'up' or 'down'
 * @param string $plugin plugin alias
 * @param array $bootstraps current bootstrap order
 * @return array|string array when successful, string contains error message
 */
	protected function _move($dir, $plugin, $bootstraps) {
		$index = array_search($plugin, $bootstraps);

		if ($dir === 'up') {
			if ($index) {
				$swap = $bootstraps[$index - 1];
			}
			if ($index == 0 || $this->_isBuiltin($swap)) {
				return __d('croogo', '%s is already at the first position', $plugin);
			}
			$before = array_slice($bootstraps, 0, $index - 1);
			$after = array_slice($bootstraps, $index + 1);
			$dependencies = $this->getDependencies($plugin);
			if (in_array($swap, $dependencies)) {
				return __d('croogo', 'Plugin %s depends on %s', $plugin, $swap);
			}
			$reordered = array_merge($before, (array)$plugin, (array)$swap);
		} elseif ($dir === 'down') {
			if ($index >= count($bootstraps) - 1) {
				return __d('croogo', '%s is already at the last position', $plugin);
			}
			$swap = $bootstraps[$index + 1];
			$before = array_slice($bootstraps, 0, $index);
			$after = array_slice($bootstraps, $index + 2);
			$dependencies = $this->getDependencies($swap);
			if (in_array($plugin, $dependencies)) {
				return __d('croogo', 'Plugin %s depends on %s', $swap, $plugin);
			}
			$reordered = array_merge($before, (array)$swap, (array)$plugin);
		} else {
			return __d('croogo', 'Invalid direction');
		}
		$reordered = array_merge($reordered, $after);

		if ($this->_isBuiltin($swap)) {
			return __d('croogo', 'Plugin %s cannot be reordered', $swap);
		}

		return $reordered;
	}

/**
 * Write Hook.bootstraps settings to database and json file
 *
 * @param array $bootstrap array of plugin aliases
 * @return boolean
 */
	protected function _saveBootstraps($bootstraps) {
		return $this->Setting->write('Hook.bootstraps', implode(',', $bootstraps));
	}

/**
 * Move plugin in the bootstrap order
 *
 * @param string $dir direction 'up' or 'down'
 * @param string $plugin plugin alias
 * @param array $bootstraps array of plugin aliases
 * @return string|bool true when successful, string contains error message
 */
	public function move($dir, $plugin, $bootstraps = null) {
		if (empty($bootstraps)) {
			$bootstraps = explode(',', Configure::read('Hook.bootstraps'));
		}
		$reordered = $this->_move(strtolower($dir), $plugin, $bootstraps);
		if (is_string($reordered)) {
			return $reordered;
		}
		return $this->_saveBootstraps($reordered);
	}

}
