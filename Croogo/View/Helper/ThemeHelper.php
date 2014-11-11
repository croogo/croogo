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

		$settings = array();
		if (isset($this->_themeData['settings'])) {
			$settings = $this->_themeData['settings'];
		}

		parent::__construct($View);
	}

/**
 * Setup deprecated view variables
 *
 * @param string $viewFile The view file that is going to be rendered
 * @return void
 */
	public function beforeRender($viewFile = null) {
		// TODO: Remove in 2.3
		$this->_View->set('themeSettings', $this->_themeSettings);
	}

/**
 * Helper method to retrieve css settings as configured in theme.json
 *
 * @param string $class Name of class/configuration to retrieve
 * @return string
 */
	public function css($class = null) {
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

}
