<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');
App::uses('Model', 'Model');
App::uses('CroogoAppModel', 'Croogo.Model');
App::uses('User', 'Model');
App::uses('AuthComponent', 'Controller/Component');

/**
 * CroogoAppModelTest file
 *
 * This file is to test the CroogoAppModel
 *
 * @category Test
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoAppModelTest extends CroogoTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.users.aco',
		'plugin.users.aro',
		'plugin.users.aros_aco',
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