<?php

App::uses('AclPermission', 'Acl.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class AclPermissionTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.users.aro',
		'plugin.users.aco',
		'plugin.users.aros_aco',
		'plugin.users.role',
		'plugin.users.user',
	);

/**
 * setUp
 */
	public function setUp() {
		parent::setUp();
		$this->Permission = ClassRegistry::init('Acl.AclPermission');
		$this->Permission->allow(
			array('model' => 'Role', 'foreign_key' => 1),
			'controllers/AclActions'
		);
	}

/**
 * testPermissionCacheClearedAfterSave
 */
	public function testPermissionCacheClearedAfterSave() {
		$key = 'permission_cache';
		$value = 'cached valued';
		$config = 'permissions';
		$result = Cache::write($key, $value, $config);
		$this->assertTrue($result);

		$result = Cache::read($key, $config);
		$this->assertEquals($value, $result);

		$this->Permission->allow(
			array('model' => 'Role', 'foreign_key' => 1),
			'controllers/AclActions'
		);

		$expected = false;
		$result = Cache::read($key, $config);
		$this->assertEquals($expected, $result);
	}

/**
 * testNoDuplicateActions
 */
	public function testNoDuplicateActions() {
		$permissions = $this->Permission->getAllowedActionsByUserId(3);
		$expected = count(array_unique($permissions));
		$this->assertEquals($expected, count($permissions));
	}

}
