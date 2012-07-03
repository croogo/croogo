<?php
App::uses('Folder', 'Utility');
App::uses('CroogoComposer', 'Extensions.Lib');

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
 * Hold instance of CroogoComposer
 *
 * @var CroogoComposer
 */
	protected $_CroogoComposer = null;

/**
 * __construct
 */
	public function __construct() {
		$this->_CroogoComposer = new CroogoComposer();
	}

/**
 * Get Plugin Name from zip file
 *
 * @param string $path Path to zip file of plugin
 * @return string Plugin name
 * @throws CakeException
 */
	public function getPluginName($path = null) {
		if (empty($path)) {
			throw new CakeException(__('Invalid plugin path'));
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
				foreach ($searches as $search) {
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
 * @throws CakeException
 */
	public function extractPlugin($path = null, $plugin = null) {
		if (!file_exists($path)) {
			throw new CakeException(__('Invalid plugin file path'));
		}

		if (empty($plugin)) {
			$plugin = $this->getPluginName($path);
		}

		$pluginHome = App::path('Plugin');
		$pluginHome = reset($pluginHome);
		$pluginPath = $pluginHome . $plugin . DS;
		if (is_dir($pluginPath)) {
			throw new CakeException(__('Plugin already exists'));
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
 * @throws CakeException
 */
	public function getThemeName($path = null) {
		if (empty($path)) {
			throw new CakeException(__('Invalid theme path'));
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
 * @throws CakeException
 */
	public function extractTheme($path = null, $theme = null) {
		if (!file_exists($path)) {
			throw new CakeException(__('Invalid theme file path'));
		}

		if (empty($theme)) {
			$theme = $this->getThemeName($path);
		}

		$themeHome = App::path('View');
		$themeHome = reset($themeHome) . 'Themed' . DS;
		$themePath = $themeHome . $theme . DS;
		if (is_dir($themePath)) {
			throw new CakeException(__('Theme already exists'));
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

/**
 * Install packages with CroogoComposer
 *
 * @param array $data
 * @return boolean
 */
	public function composerInstall($data = array()) {
		$data = array_merge(array(
			'package' => '',
			'version' => '*',
			'type' => 'plugin',
		), $data);
		if (empty($data['package']) || strpos($data['package'], '/') === false) {
			throw new CakeException(__('Must specify a valid package name: vendor/name.'));
		}
		// TODO: Enable theme support when custom install paths are enabled in composer/installers
		if ($data['type'] == 'theme') {
			throw new CakeException(__('Themes installed via composer are not yet supported.'));
		}
		$this->_CroogoComposer->getComposer();
		$this->_CroogoComposer->setConfig(array(
			$data['package'] => $data['version'],
		));
		$this->_CroogoComposer->runComposer();
		return true;
	}
}