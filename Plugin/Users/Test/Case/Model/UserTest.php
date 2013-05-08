<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');
App::uses('Model', 'Model');
App::uses('AppModel', 'Model');
App::uses('User', 'Users.Model');
App::uses('AuthComponent', 'Controller/Component');

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
		'plugin.users.aco',
		'plugin.users.aro',
		'plugin.users.aros_aco',
		'plugin.users.role',
		'plugin.users.user',
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
 * testValidIdenticalPassword method
 *
 * @return void
 */
	public function testValidIdenticalPassword() {
		$this->User->data['User'] = array('password' => '123456');
		$this->assertTrue($this->User->validIdentical(array('verify_password' => '123456')));
		$this->User->data['User'] = array('password' => '123456');
		$this->assertContains('Passwords do not match. Please, try again.', $this->User->validIdentical(array('verify_password' => 'other-value')));
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

/**
 * testDisplayFields
 *
 * @return void
 */
	public function testDisplayFields() {
		$result = $this->User->displayFields();
		$expected = array(
			'id' => array(
				'label' => 'Id',
				'sort' => true,
				'type' => 'text',
				'url' => array(),
				'options' => array(),
			),
			'username' => array(
				'label' => 'Username',
				'sort' => true,
				'type' => 'text',
				'url' => array(),
				'options' => array(),
			),
			'name' => array(
				'label' => 'Name',
				'sort' => true,
				'type' => 'text',
				'url' => array(),
				'options' => array(),
			),
			'email' => array(
				'label' => 'Email',
				'sort' => true,
				'type' => 'text',
				'url' => array(),
				'options' => array(),
			),
			'status' => array(
				'label' => 'Status',
				'sort' => true,
				'type' => 'boolean',
				'url' => array(),
				'options' => array(),
			),
			'Role.title' => array(
				'label' => 'Role',
				'sort' => true,
				'type' => 'text',
				'url' => array(),
				'options' => array(),
			),
		);
		$this->assertEquals($expected, $result);

		$result = $this->User->displayFields(array(
			'one', 'two', 'three',
		));
		$expected = array(
			'one' => array(
				'label' => 'One',
				'sort' => true,
				'type' => 'text',
				'url' => array(),
				'options' => array(),
			),
			'two' => array(
				'label' => 'Two',
				'sort' => true,
				'type' => 'text',
				'url' => array(),
				'options' => array(),
			),
			'three' => array(
				'label' => 'Three',
				'sort' => true,
				'type' => 'text',
				'url' => array(),
				'options' => array(),
			),
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testEditFields
 *
 * @return void
 */
	public function testEditFields() {
		$result = $this->User->editFields();
		$expected = array(
			'role_id' => array(),
			'username' => array(),
			'name' => array(),
			'email' => array(),
			'website' => array(),
			'status' => array(),
		);
		$this->assertEquals($expected, $result);

		$result = $this->User->editFields(array());
		$expected = array(
			'role_id' => array(),
			'username' => array(),
			'password' => array(),
			'name' => array(),
			'email' => array(),
			'website' => array(),
			'activation_key' => array(),
			'image' => array(),
			'bio' => array(),
			'timezone' => array(),
			'status' => array(),
			'updated' => array(),
			'created' => array(),
		);
		$this->assertEquals($expected, $result);

		$expected = array(
			'field' => array(
				'label' => 'My Field',
				'type' => 'select',
				'options' => array(1, 2, 3),
			),
		);
		$result = $this->User->editFields($expected);
		$this->assertEquals($expected, $result);
	}

/**
 * testDeleteAdminUsers
 */
	public function testDeleteAdminUsers() {
		// delete an admin
		$this->User->id = 2;
		$result = $this->User->delete();
		$this->assertTrue($result);

		// delete last remaining admin
		$this->User->id = 1;
		$result = $this->User->delete();
		$this->assertFalse($result);

		// delete normal user
		$this->User->id = 3;
		$result = $this->User->delete();
		$this->assertTrue($result);

		$count = $this->User->find('count');
		$this->assertEquals(1, $count);
	}

/**
 * testDeleteUsers
 */
	public function testDeleteUsers() {
		// delete normal user
		$this->User->id = 3;
		$result = $this->User->delete();
		$this->assertTrue($result);

		// delete an admin
		$this->User->id = 2;
		$result = $this->User->delete();
		$this->assertTrue($result);

		// delete last remaining admin
		$this->User->id = 1;
		$result = $this->User->delete();
		$this->assertFalse($result);

		$count = $this->User->find('count');
		$this->assertEquals(1, $count);
	}

}