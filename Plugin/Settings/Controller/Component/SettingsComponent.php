<?php

/**
 * Settings Component
 *
 * PHP version 5
 *
 * @category Component
 * @package  Croogo.Settings.Controller.Component
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class SettingsComponent extends Component {

/**
 * @var Controller
 */
	protected $_controller;

/**
 * startup
 */
	public function startup(Controller $controller) {
		$this->_controller = $controller;
		$controller->loadModel('Settings.Setting');
	}

}
