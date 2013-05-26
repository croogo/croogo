<?php

App::uses('CakeSession', 'Model/Datasource');
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
class CroogoControllerTestCase extends ControllerTestCase {

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

		if (!isset($_SERVER['REMOTE_ADDR'])) {
			$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
		}

		CakePlugin::unload('Install');
		CakePlugin::load(array('Users'), array('bootstrap' => true));
		CakePlugin::load('Example');
		Configure::write('Acl.database', 'test');
		$Setting = ClassRegistry::init('Settings.Setting');
		$Setting->settingsPath = $appDir . 'Config' . DS . 'settings.json';
		Configure::drop('settings');
		Configure::config('settings', new CroogoJsonReader(dirname($Setting->settingsPath) . DS ));
		CakeLog::drop('stdout');
		CakeLog::drop('stderr');
		$Setting->writeConfiguration();
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		CakeSession::clear();
		CakeSession::destroy();
		ClassRegistry::flush();
	}

/**
 * authUserCallback
 *
 * @param type $key
 * @return mixed
 */
	public function authUserCallback($key) {
		$auth = array(
			'id' => 1,
			'username' => 'admin',
			'role_id' => 1,
			'name' => 'Administrator',
			'email' => 'you@your-site.com',
			'website' => '/about'
		);
		if (empty($key) || !isset($auth[$key])) {
			return $auth;
		}
		return $auth[$key];
	}

/**
 * Helper to expect a Session->setFlash and redirect
 *
 * @param string $message expected message that will be passed to setFlash()
 * @param string $class class name, when null current class will be used
 * @param array $flashOptions expected SessionComponent::setFlash arguments
 */
	public function expectFlashAndRedirect($message = '', $class = false, $flashOptions = array()) {
		if (!$class) {
			$class = substr(get_class($this), 0, -4);
		}
		$flashOptions = Hash::merge(array(
			'element' => 'default',
			'params' => array(
				'class' => 'success',
			),
		), $flashOptions);
		$this->{$class}->Session
			->expects($this->once())
			->method('setFlash')
			->with(
				$this->equalTo($message),
				$this->equalTo($flashOptions['element']),
				$this->equalTo($flashOptions['params'])
			);
		$this->{$class}
			->expects($this->once())
			->method('redirect');
	}

}
