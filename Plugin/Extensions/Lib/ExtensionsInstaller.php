<?php
App::uses('Folder', 'Utility');

/**
 * Extentions Installer
 *
 * @category Extensions.Model
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExtensionsInstaller {
/**
 * Cache last retrieved plugin names for paths
 *
 * @var array
 */
	protected $_pluginName = array();

/**
 * Holds the found root path of the last checked zip file
 *
 * @var string
 */
	protected $_rootPath = '';

/**
 * Get Plugin Name from zip file
 *
 * @param string $path Path to zip file
 * @return string Plugin name
 */
	public function getPluginName($path = null) {
		if (empty($path)) {
			throw new CakeException(__d('extensions', 'Invalid plugin path'));
			return false;
		}
		if (isset($this->_pluginName[$path])) {
			return $this->_pluginName[$path];
		}
		$Zip = new ZipArchive;
		if ($Zip->open($path) === true) {
			$searches = array(
				'Config*Activation',
				'Controller*AppController',
				'Model*AppModel',
				'View*AppHelper',
			);
			for ($i = 0; $i < $Zip->numFiles; $i++) {
				$file = $Zip->getNameIndex($i);
				foreach ($searches AS $search) {
					$search = str_replace('*', '\/([\w]+)', $search);
					if (preg_match('/' . $search . '\.php/', $file, $matches)) {
						$plugin = $matches[1];
						$this->_rootPath = str_replace($matches[0], '', $file);
						break 2;
					}
				}
			}
			$Zip->close();
			if (!$plugin) {
				throw new CakeException(__d('extensions', 'Invalid plugin'));
				return false;
			}
			$this->_pluginName[$path] = $plugin;
			return $plugin;
		} else {
			throw new CakeException(__d('extensions', 'Invalid zip archive'));
		}
		return false;
	}

/**
 * Extract a plugin from a zip file
 *
 * @param string $path Path to extension zip file
 * @param string $plugin Optional plugin name
 * @return boolean
 */
	public function extractPlugin($path = null, $plugin = null) {
		if (!file_exists($path)) {
			throw new CakeException(__d('extensions', 'Invalid plugin file path'));
			return false;
		}

		if (empty($plugin)) {
			$plugin = $this->getPluginName($path);
		}

		$pluginHome = current(App::path('Plugin'));
		$pluginPath = $pluginHome . $plugin . DS;
		if (is_dir($pluginPath)) {
			throw new CakeException(__d('extensions', 'Plugin already exists'));
			return false;
		}

		$Zip = new ZipArchive;
		if ($Zip->open($path) === true) {
			new Folder($pluginPath, true);
			$Zip->extractTo($pluginPath);
			if (!empty($this->_rootPath)) {
				$old = $pluginPath . $this->_rootPath;
				$new = $pluginPath;
				$Folder = new Folder($old);
				$Folder->move($new);
			}
			$Zip->close();
			return true;
		} else {
			throw new CakeException(__d('extensions', 'Failed to extract plugin'));
		}
		return false;
	}
}