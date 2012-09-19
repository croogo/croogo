<?php
App::uses('CroogoEventManager', 'Event');
App::uses('ClassRegistry', 'Utility');
App::uses('Folder', 'Utility');
App::uses('MigrationVersion', 'Migrations.Lib');

/**
 * CroogoPlugin utility class
 *
 * PHP version 5
 *
 * @category Component
 * @package  Croogo
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
					$this->folder->path = $pluginPath . $pluginFolder . DS . 'Config';
					if (!file_exists($this->folder->path)) {
						continue;
					}
					$pluginFolderContent = $this->folder->read();
					if (in_array('plugin.json', $pluginFolderContent[1])) {
						$plugins[$pluginFolder] = $pluginFolder;
					}
				}
			}
		}
		return $plugins;
	}

/**
 * Get the content of plugin.json file of a plugin
 *
 * @param string $alias plugin folder name
 * @return array
 */
	public function getData($alias = null) {
		$pluginPaths = App::path('plugins');
		foreach ($pluginPaths as $pluginPath) {
			$manifestFile = $pluginPath . $alias . DS . 'Config' . DS . 'plugin.json';
			if (file_exists($manifestFile)) {
				$pluginData = json_decode(file_get_contents($manifestFile), true);
				if (!empty($pluginData)) {
					$pluginData['active'] = $this->isActive($alias);
					$pluginData['needMigration'] = $this->needMigration($alias, $pluginData['active']);
				} else {
					$pluginData = array();
				}
				return $pluginData;
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
 * Check if plugin is dependent on any other plugin.
 * If yes, check if that plugin is available in plugins directory.
 *
 * @param  string $plugin plugin alias (underscrored)
 * @return boolean
 */
	public function checkDependency($plugin = null) {
		$pluginData = $this->getPluginData($plugin);
		$pluginPaths = App::path('plugins');
		if (isset($pluginData['dependencies']['plugins']) && is_array($pluginData['dependencies']['plugins'])) {
			foreach ($pluginData['dependencies']['plugins'] as $p) {
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
		}
		return true;
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
 * @param strign $plugin Plugin name
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
			$plugins = $hookBootstraps;
		} else {
			$plugins[] = $plugin;
			$plugins = implode(',', $plugins);
		}
		$this->Setting->write('Hook.bootstraps', $plugins);
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

		if (count($plugins) == 0) {
			$plugins = '';
		} else {
			$plugins = implode(',', $plugins);
		}
		$this->Setting->write('Hook.bootstraps', $plugins);
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
			return __('Plugin "%s" is already active.', $plugin);
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
				Cache::delete('EventHandlers', 'setting_write_configuration');
				return true;
			} else {
				return __('Plugin "%s" depends on "%s" plugin.', $plugin, $missingPlugin);
			}
			return __('Plugin "%s" could not be activated. Please, try again.', $plugin);
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
			return __('Plugin "%s" is not active.', $plugin);
		}
		$pluginActivation = $this->getActivator($plugin);
		if (!isset($pluginActivation) ||
			(isset($pluginActivation) && method_exists($pluginActivation, 'beforeDeactivation') && $pluginActivation->beforeDeactivation($this->_Controller))) {
			$this->removeBootstrap($plugin);
			if (isset($pluginActivation) && method_exists($pluginActivation, 'onDeactivation')) {
				$pluginActivation->onDeactivation($this->_Controller);
			}
			CroogoPlugin::unload($plugin);
			Cache::delete('EventHandlers', 'setting_write_configuration');
			return true;
		} else {
			return __('Plugin could not be deactivated. Please, try again.');
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
 */
	public function delete($plugin) {
		$pluginPath = APP . 'Plugin' . DS . $plugin;
		$folder = new Folder();
		$result = $folder->delete($pluginPath);
		if ($result !== true) {
			return $folder->errors();
		}
		return true;
	}

}
