<?php

/**
 * CroogoTheme class
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
class CroogoTheme extends Object {

/**
 * Constructor
 */
	public function __construct() {
		$this->Setting = ClassRegistry::init('Settings.Setting');
	}

/**
 * Get theme aliases (folder names)
 *
 * @return array
 */
	public function getThemes() {
		$themes = array(
			'default' => 'default',
		);
		$this->folder = new Folder;
		$viewPaths = App::path('views');
		$expected = array('name' => '', 'description' => '');
		foreach ($viewPaths as $viewPath) {
			$this->folder->path = $viewPath . 'Themed';
			$themeFolders = $this->folder->read();
			foreach ($themeFolders['0'] as $themeFolder) {
				$this->folder->path = $viewPath . 'Themed' . DS . $themeFolder . DS . 'webroot';
				$themeFolderContent = $this->folder->read();
				$themeJson = $this->folder->path . DS . 'theme.json';
				if (in_array('theme.json', $themeFolderContent['1'])) {
					$contents = file_get_contents($themeJson);
					$json = json_decode($contents, true);
					$intersect = array_intersect_key($expected, $json);
					if ($json !== null && $intersect == $expected) {
						$themes[$themeFolder] = $themeFolder;
					}
				}
			}
		}
		return $themes;
	}

/**
 * Get the content of theme.json file from a theme
 *
 * @param string $alias theme folder name
 * @return array
 */
	public function getData($alias = null) {
		if ($alias == null || $alias == 'default') {
			$manifestFile = CakePlugin::path('Croogo') . 'webroot' . DS . 'theme.json';
		} else {
			$viewPaths = App::path('views');
			foreach ($viewPaths as $viewPath) {
				if (file_exists($viewPath . 'Themed' . DS . $alias . DS . 'webroot' . DS . 'theme.json')) {
					$manifestFile = $viewPath . 'Themed' . DS . $alias . DS . 'webroot' . DS . 'theme.json';
					continue;
				}
			}
			if (!isset($manifestFile)) {
				$manifestFile = CakePlugin::path('Croogo') . 'webroot' . DS . 'theme.json';
			}
		}
		if (isset($manifestFile) && file_exists($manifestFile)) {
			$themeData = json_decode(file_get_contents($manifestFile), true);
			if ($themeData == null) {
				$themeData = array();
			}
		} else {
			$themeData = array();
		}
		return $themeData;
	}

/**
 * Get the content of theme.json file from a theme
 *
 * @param string $alias theme folder name
 * @return array
 * @deprecated use getData()
 */
	public function getThemeData($alias = null) {
		return $this->getData($alias);
	}

/**
 * Activate theme $alias
 * @param $alias theme alias
 * @return mixed On success Setting::$data or true, false on failure
 */
	public function activate($alias) {
		if ($alias == 'default' || $alias == null) {
			$alias = '';
		}
		return $this->Setting->write('Site.theme', $alias);
	}

/**
 * Delete theme
 *
 * @param string $alias Theme alias
 * @return boolean true when successful, false or array or error messages when failed
 * @throws InvalidArgumentException
 * @throws UnexpectedValueException
 */
	public function delete($alias) {
		if (empty($alias)) {
			throw new InvalidArgumentException(__d('croogo', 'Invalid theme'));
		}
		$paths = array(
			APP . 'webroot' . DS . 'theme' . DS . $alias,
			APP . 'View' . DS . 'Themed' . DS . $alias,
		);
		$folder = new Folder;
		foreach ($paths as $path) {
			if (!file_exists($path)) {
				continue;
			}
			if (is_link($path)) {
				return unlink($path);
			} elseif (is_dir($path)) {
				if ($folder->delete($path)) {
					return true;
				} else {
					return $folder->errors();
				}
			}
		}
		throw new UnexpectedValueException(__d('croogo', 'Theme %s not found', $alias));
	}

}
