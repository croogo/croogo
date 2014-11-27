<?php

/**
 * CroogoTheme class
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
			if (!is_dir($this->folder->path)) {
				continue;
			}
			$themeFolders = $this->folder->read();
			foreach ($themeFolders['0'] as $themeFolder) {
				$themeRoot = $viewPath . 'Themed' . DS . $themeFolder . DS;

				$composerJson = $themeRoot . 'composer.json';
				$this->folder->path = $themeRoot;
				$themeRootFolderContent = $this->folder->read();
				if (in_array('composer.json', $themeRootFolderContent['1'])) {
					$contents = file_get_contents($composerJson);
					$json = json_decode($contents, true);
					if (isset($json['type']) && $json['type'] === 'croogo-theme') {
						$themes[$themeFolder] = $themeFolder;
						continue;
					}
				}

				$themeWebroot = $themeRoot . 'webroot' . DS;
				$themeConfig = $themeRoot . 'Config' . DS;
				if (!is_dir($themeWebroot) && !is_dir($themeConfig)) {
					continue;
				}

				$paths = array($themeConfig, $themeWebroot);
				foreach ($paths as $path) {
					$this->folder->path = $path;
					$themeFolderContent = $this->folder->read();
					if (in_array('theme.json', $themeFolderContent['1'])) {
						$themeJson = $path . 'theme.json';
						$contents = file_get_contents($themeJson);
						$json = json_decode($contents, true);
						if ($json === null) {
							$this->log('Invalid theme manifest:' . $themeJson);
							$json = array();
						}
						$intersect = array_intersect_key($expected, $json);
						if ($json !== null && $intersect == $expected) {
							$themes[$themeFolder] = $themeFolder;
						}
						continue;
					}
				}
			}
		}
		return $themes;
	}

/**
 * Get the content of theme.json or composer.json file from a theme
 *
 * @param string $alias theme folder name
 * @return array
 */
	public function getData($alias = null) {
		$themeData = array(
			'name' => $alias,
			'regions' => array(),
			'screenshot' => null,
			'settings' => array(
				'css' => array(
					'columnFull' => 'span12',
					'columnLeft' => 'span8',
					'columnRight' => 'span4',
					'container' => 'container-fluid',
					'dashboardFull' => 'span12',
					'dashboardLeft' => 'span6',
					'dashboardRight' => 'span6',
					'dashboardClass' => 'sortable-column',
					'formInput' => 'input-block-level',
					'imageClass' => '',
					'row' => 'row-fluid',
					'tableClass' => 'table',
					'thumbnailClass' => 'img-polaroid',
				),
				'iconDefaults' => array(
					'classDefault' => '',
					'classPrefix' => 'icon-',
					'largeIconClass' => 'icon-large',
					'smallIconClass' => '',
				),
				'icons' => array(
					'attach' => 'paper-clip',
					'check-mark' => 'ok',
					'comment' => 'comment-alt',
					'copy' => 'copy',
					'create' => 'plus',
					'delete' => 'trash',
					'error-sign' => 'exclamation-sign',
					'home' => 'home',
					'info-sign' => 'info-sign',
					'inspect' => 'zoom-in',
					'link' => 'link',
					'move-down' => 'chevron-down',
					'move-up' => 'chevron-up',
					'power-off' => 'off',
					'power-on' => 'bolt',
					'question-sign' => 'question-sign',
					'read' => 'eye-open',
					'refresh' => 'refresh',
					'resize' => 'resize-small',
					'search' => 'search',
					'success-sign' => 'ok-sign',
					'translate' => 'flag',
					'update' => 'pencil',
					'upload' => 'upload-alt',
					'warning-sign' => 'warning-sign',
					'x-mark' => 'remove',
				),
				'prefixes' => array(
					'' => array(
						'helpers' => array(
							'Html' => array(),
							'Form' => array(),
						),
					),
					'admin' => array(
						'helpers' => array(
							'Html' => array(
								'className' => 'Croogo.CroogoHtml',
							),
							'Form' => array(
								'className' => 'Croogo.CroogoForm',
							),
							'Paginator' => array(
								'className' => 'Croogo.CroogoPaginator',
							),
						),
					),
				),
			),
		);
		$default = CakePlugin::path('Croogo') . 'webroot' . DS . 'theme.json';

		if ($alias == null || $alias == 'default') {
			$manifestFile = $default;
		} else {
			$viewPaths = App::path('views');
			foreach ($viewPaths as $viewPath) {
				$themeRoot = $viewPath . 'Themed' . DS . $alias . DS;
				$themeJson = $themeRoot . 'Config' . DS . 'theme.json';
				if (file_exists($themeJson)) {
					$manifestFile = $themeJson;
				} else {
					$themeJson = $themeRoot . 'webroot' . DS . 'theme.json';
					if (file_exists($themeJson)) {
						$manifestFile = $themeJson;
					}
				}

				if (file_exists($themeRoot . 'composer.json')) {
					$composerJson = $themeRoot . 'composer.json';
				}

				if (isset($manifestFile) || isset($composerJson)) {
					break;
				}
			}
		}

		if (!isset($manifestFile) && !isset($composerJson)) {
			return array();
		}

		if (isset($manifestFile)) {
			$json = json_decode(file_get_contents($manifestFile), true);
			if ($json) {
				$themeData = Hash::merge($themeData, $json);
			}
		}

		if (isset($composerJson)) {
			$json = json_decode(file_get_contents($composerJson), true);
			if ($json) {
				$json['vendor'] = $json['name'];
				unset($json['name']);
				$themeData = Hash::merge($themeData, $json);
			}
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
		Cache::delete('file_map', '_cake_core_');
		$Setting = ClassRegistry::init('Settings.Setting');
		return $Setting->write('Site.theme', $alias);
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

/**
 * Helper method to retrieve given $theme settings
 *
 * @param string $theme Theme name
 * @return array Theme configuration data
 */
	public static function config($theme = null) {
		static $croogoTheme = null;
		static $themeData = array();
		if ($croogoTheme === null) {
			$croogoTheme = new CroogoTheme();
		}

		if (empty($themeData[$theme])) {
			$data = $croogoTheme->getData($theme);
			$request = Router::getRequest();
			if ($request) {
				$prefix = $request->param('prefix');
				if (isset($data['settings']['prefixes'][$prefix]['css'])) {
					$data['settings']['css'] = Hash::merge($data['settings']['prefixes'][$prefix]['css'],
						$data['settings']['css']
					);
				}
			}
			$themeData[$theme] = $data;
		}

		return $themeData[$theme];
	}

}
