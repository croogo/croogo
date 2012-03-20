<?php
App::uses('User', 'Model');
App::uses('AuthComponent', 'Controller/Component');
class UserTest extends CakeTestCase {

	public $fixtures = array(
		'app.aco',
		'app.aro',
		'app.aros_aco',
		'app.role',
		'app.user',
	);

	public function setUp() {
		parent::setUp();
		$this->User = ClassRegistry::init('User');
		$this->User->Aro->useDbConfig = 'test';
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->User);
	}

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
	}

}