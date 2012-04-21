<?php
App::uses('Folder', 'Utility');

/**
 * Extensions Installer
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
 * Cache last retrieved theme names for paths
 *
 * @var array
 */
	protected $_themeName = array();

/**
 * Holds the found root paths of checked zip file
 *
 * @var array
 */
	protected $_rootPath = array();

/**
 * Get Plugin Name from zip file
 *
 * @param string $path Path to zip file of plugin
 * @return string Plugin name
 */
	public function getPluginName($path = null) {
		if (empty($path)) {
			throw new CakeException(__('Invalid plugin path'));
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
						$plugin = trim($matches[1]);
						$this->_rootPath[$path] = str_replace($matches[0], '', $file);
						break 2;
					}
				}
			}
			$Zip->close();
			if (!$plugin) {
				throw new CakeException(__('Invalid plugin'));
				return false;
			}
			$this->_pluginName[$path] = $plugin;
			return $plugin;
		} else {
			throw new CakeException(__('Invalid zip archive'));
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
			throw new CakeException(__('Invalid plugin file path'));
			return false;
		}

		if (empty($plugin)) {
			$plugin = $this->getPluginName($path);
		}

		$pluginHome = current(App::path('Plugin'));
		$pluginPath = $pluginHome . $plugin . DS;
		if (is_dir($pluginPath)) {
			throw new CakeException(__('Plugin already exists'));
			return false;
		}

		$Zip = new ZipArchive;
		if ($Zip->open($path) === true) {
			new Folder($pluginPath, true);
			$Zip->extractTo($pluginPath);
			if (!empty($this->_rootPath[$path])) {
				$old = $pluginPath . $this->_rootPath[$path];
				$new = $pluginPath;
				$Folder = new Folder($old);
				$Folder->move($new);
			}
			$Zip->close();
			return true;
		} else {
			throw new CakeException(__('Failed to extract plugin'));
		}
		return false;
	}

/**
 * Get name of theme
 *
 * @param string $path Path to zip file of theme
 */
	public function getThemeName($path = null) {
		if (empty($path)) {
			throw new CakeException(__('Invalid theme path'));
			return false;
		}
		if (isset($this->_themeName[$path])) {
			return $this->_themeName[$path];
		}
		$Zip = new ZipArchive;
		if ($Zip->open($path) === true) {
			$search = 'webroot/theme.yml';
			for ($i = 0; $i < $Zip->numFiles; $i++) {
				$file = $Zip->getNameIndex($i);
				if (stripos($file, $search) !== false) {
					$this->_rootPath[$path] = str_replace($search, '', $file);
					$yml = $Zip->getFromIndex($i);
					preg_match('/name: (.+)/', $yml, $matches);
					if (empty($matches[1])) {
						throw new CakeException(__('Invalid YML file'));
					} else {
						$theme = trim($matches[1]);
					}
					break;
				}
			}
			$Zip->close();
			if (!$theme) {
				throw new CakeException(__('Invalid theme'));
				return false;
			}
			$this->_themeName[$path] = $theme;
			return $theme;
		} else {
			throw new CakeException(__('Invalid zip archive'));
		}
		return false;
	}

/**
 * Extract a theme from a zip file
 *
 * @param string $path Path to extension zip file
 * @param string $theme Optional theme name
 * @return boolean
 */
	public function extractTheme($path = null, $theme = null) {
		if (!file_exists($path)) {
			throw new CakeException(__('Invalid theme file path'));
			return false;
		}

		if (empty($theme)) {
			$theme = $this->getThemeName($path);
		}

		$themeHome = current(App::path('View')) . 'Themed' . DS;
		$themePath = $themeHome . $theme . DS;
		if (is_dir($themePath)) {
			throw new CakeException(__('Theme already exists'));
			return false;
		}

		$Zip = new ZipArchive;
		if ($Zip->open($path) === true) {
			new Folder($themePath, true);
			$Zip->extractTo($themePath);
			if (!empty($this->_rootPath[$path])) {
				$old = $themePath . $this->_rootPath[$path];
				$new = $themePath;
				$Folder = new Folder($old);
				$Folder->move($new);
			}
			$Zip->close();
			return true;
		} else {
			throw new CakeException(__('Failed to extract theme'));
		}
		return false;
	}
}