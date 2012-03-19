<?php

/**
 * CroogoTheme class
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
class CroogoTheme extends Object {

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
		foreach ($viewPaths AS $viewPath) {
			$this->folder->path = $viewPath . 'Themed';
			$themeFolders = $this->folder->read();
			foreach ($themeFolders['0'] AS $themeFolder) {
				$this->folder->path = $viewPath . 'Themed' . DS . $themeFolder . DS . 'webroot';
				$themeFolderContent = $this->folder->read();
				if (in_array('theme.json', $themeFolderContent['1'])) {
					$themes[$themeFolder] = $themeFolder;
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
	public function getThemeData($alias = null) {
		if ($alias == null || $alias == 'default') {
			$manifestFile = WWW_ROOT . 'theme.json';
		} else {
			$viewPaths = App::path('views');
			foreach ($viewPaths AS $viewPath) {
				if (file_exists($viewPath . 'Themed' . DS . $alias . DS . 'webroot' . DS . 'theme.json')) {
					$manifestFile = $viewPath . 'Themed' . DS . $alias . DS . 'webroot' . DS . 'theme.json';
					continue;
				}
			}
			if (!isset($manifestFile)) {
				$manifestFile = WWW_ROOT . 'theme.json';
			}
		}
		if (isset($manifestFile) && file_exists($manifestFile)) {
			$themeData = json_decode(file_get_contents($manifestFile), true);
			if ($themeData == NULL) {
				$themeData = array();
			}
		} else {
			$themeData = array();
		}
		return $themeData;
	}

}
