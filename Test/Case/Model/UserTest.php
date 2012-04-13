<?php
App::uses('User', 'Model');
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');
App::uses('CroogoTestCase', 'TestSuite');

/**
 * TestUser
 *
 */
class TestUser extends User {

/**
 * model alias
 *
 * @var string
 */
	public $alias = 'User';

/**
 * identical method
 *
 * @param array $check
 * @return boolean
 */
	public function identical($check) {
		return $this->_identical($check);
	}

}

/**
 * TestUser
 *
 */
class UserTest extends CroogoTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.aco',
		'app.aro',
		'app.aros_aco',
		'app.role',
		'app.user',
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
		$this->User = ClassRegistry::init('TestUser');
		$this->User->Aro->useDbConfig = 'test';
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->User->request, $this->User);
	}

/**
 * testPasswords method
 *
 * @return void
 */
	public function testPasswords() {
		$this->User->create(array(
			'username' => 'new_user',
			'name' => 'New User',
			'role_id' => 3,
			'email' => 'contact@croogo.org',
			'password' => 'password',
			'website' => 'http://croogo.org',
			'activation_key' => md5(uniqid()),
		));
		$this->User->save();
		$this->assertEmpty($this->User->validationErrors, 'Validation error: ' . print_r($this->User->validationErrors, true));
		$newUser = $this->User->read();
		$this->assertNotEqual($newUser, false);
		$this->assertNotEqual($newUser['User']['password'], 'password');
		$this->assertEqual($newUser['User']['password'], AuthComponent::password('password'));

		$newUser['User']['password'] = '123456';
		$this->User->id = $newUser['User']['id'];
		$this->User->save($newUser);
		$this->assertEmpty($this->User->validationErrors, 'Validation error: ' . print_r($this->User->validationErrors, true));
		$newUser = $this->User->read();
		$this->assertNotEqual($newUser['User']['password'], '123456');
		$this->assertEqual($newUser['User']['password'], AuthComponent::password('123456'));

		$oldPassword = $newUser['User']['password'];
		$newUser['User']['password'] = '';
		$this->User->id = $newUser['User']['id'];
		$this->User->save($newUser);
		$this->assertContains('Passwords must be at least 6 characters long.', print_r($this->User->validationErrors, true));
		$newUser = $this->User->read();
		$this->assertEqual($newUser['User']['password'], $oldPassword);
	}

/**
 * testIdentical method
 *
 * @return void
 */
	public function testIdenticalPassword() {
		$this->User->data['User'] = array('password' => '123456');
		$this->assertTrue($this->User->identical(array('verify_password' => '123456')));
		$this->User->data['User'] = array('password' => '123456');
		$this->assertContains('Passwords do not match. Please, try again.', $this->User->identical(array('verify_password' => 'other-value')));
	}

/**
 * testDeleteLastUser method
 *
 * @return void
 */
	public function testDeleteLastUser() {
		$this->User->create(array(
			'username' => 'new_user',
			'name' => 'Admin User',
			'role_id' => 1,
			'email' => 'contact@croogo.org',
			'password' => 'password',
			'website' => 'http://croogo.org',
			'activation_key' => md5(uniqid()),
			'status' => true,
		));
		$this->User->save();
		$newUser = $this->User->read();
		$this->User->deleteAll(array('User.id !=' => $newUser['User']['id']));
		$this->assertFalse($this->User->delete($newUser['User']['id']));
	}

/**
 * testDeleteAdminUser method
 *
 * @return void
 */
	public function testDeleteAdminUser() {
		$this->User->create(array(
			'username' => 'admin_user',
			'name' => 'Admin User',
			'role_id' => 1,
			'email' => 'contact@croogo.org',
			'password' => 'password',
			'website' => 'http://croogo.org',
			'activation_key' => md5(uniqid()),
			'status' => true,
		));
		$this->User->save();
		$newAdmin = $this->User->read();
		$this->User->create(array(
			'username' => 'another_adm',
			'name' => 'Another Admin',
			'role_id' => 1,
			'email' => 'another_adm@croogo.org',
			'password' => 'password',
			'website' => 'http://croogo.org',
			'activation_key' => md5(uniqid()),
			'status' => true,
		));
		$this->User->save();
		$anotherAdmin = $this->User->read();
		$this->User->deleteAll(array('NOT' => array('User.id' => array($newAdmin['User']['id'], $anotherAdmin['User']['id']))));
		$this->assertTrue($this->User->delete($newAdmin['User']['id']));
	}

}