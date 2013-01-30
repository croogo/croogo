<?php

App::uses('CroogoTestFixture', 'TestSuite');

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

	public static function setUpBeforeClass() {
		self::_restoreSettings();
	}

	public static function tearDownAfterClass() {
		self::_restoreSettings();
	}

	protected static function _restoreSettings() {
		$source = TESTS . 'test_app' . DS . 'Config' . DS . 'settings.default';
		$target = TESTS . 'test_app' . DS . 'Config' . DS . 'settings.json';
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
			'Plugin' => array(TESTS . 'test_app' . DS . 'Plugin' . DS),
			'View' => array(TESTS . 'test_app' . DS . 'View' . DS),
		), App::PREPEND);

		CakePlugin::unload('Install');
		CakePlugin::load('Example');
		Configure::write('Acl.database', 'test');
		$Setting = ClassRegistry::init('Settings.Setting');
		$Setting->settingsPath = TESTS . 'test_app' . DS . 'Config' . DS . 'settings.json';
		Configure::drop('settings');
		Configure::config('settings', new CroogoJsonReader(dirname($Setting->settingsPath) . DS));
		$Setting->writeConfiguration();
	}

}
