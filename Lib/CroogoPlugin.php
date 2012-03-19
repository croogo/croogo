<?php

App::uses('ClassRegistry', 'Utility');
App::uses('Folder', 'Utility');

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

	public function __construct() {
		$this->Setting = ClassRegistry::init('Setting');
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
		foreach ($pluginPaths AS $pluginPath) {
			$this->folder->path = $pluginPath;
			if (!file_exists($this->folder->path)) { continue; }
			$pluginFolders = $this->folder->read();
			foreach ($pluginFolders[0] AS $pluginFolder) {
				if (substr($pluginFolder, 0, 1) != '.') {
					$this->folder->path = $pluginPath . $pluginFolder . DS . 'Config';
					if (!file_exists($this->folder->path)) { continue; }
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
	public function getPluginData($alias = null) {
		$pluginPaths = App::path('plugins');
		foreach ($pluginPaths AS $pluginPath) {
			$manifestFile = $pluginPath . $alias . DS . 'Config' . DS . 'plugin.json';
			if (file_exists($manifestFile)) {
				$pluginData = json_decode(file_get_contents($manifestFile), true);
				if (!empty($pluginData)) {
					$pluginData['active'] = $this->pluginIsActive($alias);
					unset($pluginManifest);
				} else {
					$pluginData = array();
				}
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
		$this->Setting->write('Hook.bootstraps', $plugins);
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
		$this->Setting->write('Hook.bootstraps', $plugins);
	}

}
