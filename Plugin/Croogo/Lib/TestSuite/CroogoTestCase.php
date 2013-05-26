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
		Configure::write('Config.language', 'eng');
	}

	public static function tearDownAfterClass() {
		self::_restoreSettings();
		Configure::write('Config.language', Configure::read('Site.locale'));
	}

	protected static function _restoreSettings() {
		$configDir = CakePlugin::path('Croogo') . 'Test' . DS . 'test_app' . DS . 'Config' . DS;
		$source = $configDir . 'settings.default';
		$target = $configDir . 'settings.json';
		copy($source, $target);
	}

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$appDir = CakePlugin::path('Croogo') . 'Test' . DS . 'test_app' . DS;

		App::build(array(
			'Plugin' => array($appDir . 'Plugin' . DS),
			'View' => array($appDir . 'View' . DS),
		), App::PREPEND);
		$this->_paths = App::paths();

		CakePlugin::unload('Install');
		CakePlugin::load('Example');
		Configure::write('Acl.database', 'test');
		$Setting = ClassRegistry::init('Settings.Setting');
		$Setting->settingsPath = $appDir . 'Config' . DS . 'settings.json';
		Configure::drop('settings');
		Configure::config('settings', new CroogoJsonReader(dirname($Setting->settingsPath) . DS));
		$Setting->writeConfiguration();
	}

	public function tearDown() {
		parent::tearDown();

		App::build($this->_paths);
	}

}
