<?php
App::import('Model', 'Node');

class UserTest extends CakeTestCase {

	public $fixtures = array(
		'aco',
		'aro',
		'aros_aco',
		'role',
		'user',
	);

	public function startTest() {
		 $this->User =& ClassRegistry::init('User');
	}

	public function endTest() {
		unset($this->User);
		ClassRegistry::flush();
	}

	public function testPasswords() {
		App::uses('AuthComponent', 'Controller/Component');
		$this->User->create(array(
			'username' => 'new_user',
			'name' => 'New User',
			'role_id' => 3,
			'email' => 'contact@croogo.org',
			'password' => 'password',
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