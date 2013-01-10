<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');
App::uses('Model', 'Model');
App::uses('AppModel', 'Model');
App::uses('User', 'Model');
App::uses('AuthComponent', 'Controller/Component');

/**
 * AppModelTest file
 *
 * This file is to test the AppModel
 *
 * @category Test
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AppModelTest extends CroogoTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.croogo.aco',
		'plugin.croogo.aro',
		'plugin.croogo.aros_aco',
		'plugin.users.role',
		'plugin.users.user',
		'plugin.settings.setting',
	);

/**
 * User instance
 *
 * @var TestUser
 */
	public $User;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->User = ClassRegistry::init('Users.User');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->User);
	}

/**
 * testValidName
 */
	public function testValidName() {
		$this->assertTrue($this->User->validName(array('name' => 'Kyle')));
		$this->assertFalse($this->User->validName(array('name' => 'what%is@this#i*dont!even')));
	}

/**
 * testValidAlias
 */
	public function testValidAlias() {
		$this->assertTrue($this->User->validAlias(array('name' => 'Kyle')));
		$this->assertFalse($this->User->validAlias(array('name' => 'Not an Alias')));
	}

}