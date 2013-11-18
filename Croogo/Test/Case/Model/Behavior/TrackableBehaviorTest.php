<?php

App::uses('CroogoTestCase', 'Croogo.TestSuite');
App::uses('User', 'Users.Model');

class TrackableUserModel extends User {

	public $useTable = 'users';

	public $order = 'TrackableUserModel.name';

	public $actsAs = array(
		'Croogo.Trackable' => array(
			'userModel' => 'TrackableUserModel',
		),
	);
}

class TrackableBehaviorTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.croogo.trackable',
		'plugin.users.user',
		'plugin.users.role',
		'plugin.settings.setting',
	);

	public function setUp() {
		parent::setUp();
		$this->loadFixtures('Trackable');
		$this->model = ClassRegistry::init(array(
			'class' => 'TestModel',
			'alias' => 'TestModel',
			'table' => 'trackables',
		));
		$this->model->Behaviors->attach('Croogo.Trackable');
	}

	public function tearDown() {
		Configure::delete('Trackable.Auth');
		CakeSession::delete('Auth.User');
	}

	protected function _authTrackable($userIdField = 'id', $userId = 1) {
		Configure::write('Trackable.Auth.User', array($userIdField => $userId));
	}

	protected function _authSession($userIdField = 'id', $userId = 1) {
		CakeSession::write('Auth.User', array($userIdField => $userId));
	}

/**
 * testFieldPopulation
 */
	protected function _testFieldPopulation($authCallback) {
		$this->{$authCallback}();

		$this->model->create(array('id' => 1, 'title' => 'foobar'));
		$result = $this->model->save();
		$data = $result['TestModel'];
		$this->assertNotEmpty($data['created_by']);
		$this->assertEquals($data['created_by'], $data['updated_by']);

		unset($data['created_by']);
		unset($data['created']);
		unset($data['updated_by']);
		unset($data['updated']);

		$this->{$authCallback}('id', 2);

		$data['title'] = 'spameggs';
		$this->model->save($data);

		$result = $this->model->findById(1);

		$data = $result['TestModel'];
		$this->assertTrue(array_key_exists('TrackableCreator', $result));
		$this->assertTrue(array_key_exists('TrackableUpdater', $result));
		$this->assertEquals(1, $data['created_by']);
		$this->assertEquals(2, $data['updated_by']);
	}

/**
 * Test model operation using session auth data
 */
	public function testUserDataFromSession() {
		$this->_testFieldPopulation('_authSession');
	}

/**
 * Test model operation using manually setup auth data
 */
	public function testUserDataFromTrackable() {
		$this->_testFieldPopulation('_authTrackable');
	}

/**
 * Test auth data override
 */
	public function testAuthDataOverride() {
		$this->_authTrackable('id', '3');
		$this->_authSession('id', '1');

		$this->model->create(array('id' => 1, 'title' => 'foobar'));
		$this->model->save();
		$result = $this->model->findById(1);
		$data = $result['TestModel'];
		$this->assertNotEmpty($data['created_by']);
		$this->assertEquals($data['created_by'], $data['updated_by']);
		$this->assertEquals('yvonne', $result['TrackableCreator']['username']);
	}

/**
 * Test with uncommon/inherited User model
 */
	public function testUncommonInheritedUserModel() {
		$User = ClassRegistry::init('TrackableUserModel');
		$User->Behaviors->detach('UserAro');
		$User->Behaviors->detach('Acl');

		$user = $User->findById(1);
		$this->assertTrue(isset($user['TrackableCreator']));
		$this->assertTrue(isset($user['TrackableUpdater']));

		$user['TrackableUserModel']['bio'] = 'I am the law';

		$this->_authTrackable();
		$User->id = $user['TrackableUserModel']['id'];
		$user['TrackableUserModel']['bio'] = 'I am the admin';
		unset($user['TrackableUserModel']['website']);
		$User->save($user);
		$user = $User->findById(1);

		$this->assertEquals('1', $user['TrackableUserModel']['updated_by']);
		$this->assertEquals('1', $user['TrackableUpdater']['id']);
	}

/**
 * Test Trackable saveField
 */
	public function testTrackableSaveField() {
		$User = ClassRegistry::init('TrackableUserModel');
		$User->Behaviors->detach('UserAro');
		$User->Behaviors->detach('Acl');

		$user = $User->findById(1);
		$this->assertTrue(isset($user['TrackableCreator']));
		$this->assertTrue(isset($user['TrackableUpdater']));

		$this->_authTrackable('id', 3);
		$User->id = $user['TrackableUserModel']['id'];
		$saved = $User->saveField('bio', 'Rockstar');
		$user = $User->findById(1);

		$this->assertEquals('Rockstar', $user['TrackableUserModel']['bio']);
		$this->assertEquals('3', $user['TrackableUserModel']['updated_by']);
		$this->assertEquals('3', $user['TrackableUpdater']['id']);
	}

}
