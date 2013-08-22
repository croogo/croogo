<?php

App::uses('CroogoTestCase', 'Croogo.TestSuite');

class TrackableBehaviorTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.croogo.trackable',
		'plugin.users.user',
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
	public function _testFieldPopulation($authCallback) {
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

}
