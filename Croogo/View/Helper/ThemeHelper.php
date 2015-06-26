<?php

App::uses('AppHelper', 'View/Helper');
App::uses('CroogoTheme', 'Extensions.Lib');

/**
 * Theme Helper
 *
 * @category Helper
 * @package  Croogo.Croogo.View.Helper
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ThemeHelper extends AppHelper {

	protected $_themeSettings = array();

	protected $_iconMap = array();

/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
	public $helpers = array(
	);

	public function __construct(View $View, $settings = array()) {
		$themeConfig = CroogoTheme::config($View->theme);
		$this->_themeSettings = $themeConfig['settings'];

		$this->_iconMap = $this->_themeSettings['icons'];
		$prefix = $View->request->param('prefix');
		if (isset($this->_themeSettings['prefixes'][$prefix]['helpers']['Html']['icons'])) {
			$this->_iconMap = Hash::merge(
				$this->_iconMap,
				$this->_themeSettings['prefixes'][$prefix]['helpers']['Html']['icons']
			);
		}

		parent::__construct($View);

		$this->__setupDeprecatedViewVars();
	}

/**
 * Setup deprecated view variables
 *
 * @param string $viewFile The view file that is going to be rendered
 * @return void
 */
	private function __setupDeprecatedViewVars() {
		// TODO: Remove in 2.3
		$this->_View->set('_icons', $this->_iconMap);
		$this->_View->set('themeSettings', $this->_themeSettings);
	}

/**
 * Helper method to retrieve css settings as configured in theme.json
 *
 * @param string $class Name of class/configuration to retrieve
 * @return string
 */
	public function getCssClass($class = null) {
		if ($class) {
			$class = '.' . $class;
		}
		return $this->settings('css' . $class);
	}

/**
 * Helper method to retrieve theme settings as configured in theme.json
 *
 * @param string $class Name of class/configuration to retrieve
 * @return string
 */
	public function settings($key = null) {
		$theme = $this->_View->theme ? $this->_View->theme : 'default';
		if (empty($this->_themeSettings)) {
			$this->log(sprintf('Invalid settings for theme "%s"', $theme));
			return array();
		}
		if ($key === null) {
			return $this->_themeSettings;
		}
		return Hash::get($this->_themeSettings, $key);
	}

/**
 * Returns a mapped icon identifier based on current active theme
 *
 * @param string $icon Icon name (without prefix)
 * @return string a mapped icon identifier
 */
	public function getIcon($icon) {
		$mapped = $icon;
		if (isset($this->_iconMap[$icon])) {
			$mapped = $this->_iconMap[$icon];
		}
		return $mapped;
	}
}
