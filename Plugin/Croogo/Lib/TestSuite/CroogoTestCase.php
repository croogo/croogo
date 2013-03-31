<?php

App::uses('CroogoTestFixture', 'Croogo.TestSuite');

/**
 * CroogoTestCase class
 *
 * PHP version 5
 *
 * @category TestSuite
 * @package  Croogo
 * @version  1.4
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoTestCase extends CakeTestCase {

	protected $_paths = array();

	public static function setUpBeforeClass() {
		self::_restoreSettings();
	}

	public static function tearDownAfterClass() {
		self::_restoreSettings();
	}

	protected static function _restoreSettings() {
		$root = CakePlugin::path('Croogo');
		$source = $root . 'Test' . DS . 'test_app' . DS . 'Config' . DS . 'settings.default';
		$target = $root . 'Test' . DS . 'test_app' . DS . 'Config' . DS . 'settings.json';
		copy($source, $target);
	}

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		App::build(array(
			'Plugin' => array(CakePlugin::path('Croogo') . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS),
			'View' => array(CakePlugin::path('Croogo') . 'Test' . DS . 'test_app' . DS . 'View' . DS),
		), App::PREPEND);
		$this->_paths = App::paths();

		CakePlugin::unload('Install');
		CakePlugin::load('Example');
		Configure::write('Acl.database', 'test');
		$Setting = ClassRegistry::init('Settings.Setting');
		$Setting->settingsPath = CakePlugin::path('Croogo') . 'Test' . DS . 'test_app' . DS . 'Config' . DS . 'settings.json';
		Configure::drop('settings');
		Configure::config('settings', new CroogoJsonReader(dirname($Setting->settingsPath) . DS));
		$Setting->writeConfiguration();
	}

	public function tearDown() {
		parent::tearDown();

		App::build($this->_paths);
	}

}
