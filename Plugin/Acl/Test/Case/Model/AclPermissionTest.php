<?php

App::uses('AclPermission', 'Acl.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class AclPermissionTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.croogo.aro',
		'plugin.croogo.aco',
		'plugin.croogo.aros_aco',
		'plugin.users.role',
	);

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

		$Permission = ClassRegistry::init('Acl.AclPermission');
		$Permission->allow(
			array('model' => 'Role', 'foreign_key' => 1),
			'controllers/AclActions'
		);

		$expected = false;
		$result = Cache::read($key, $config);
		$this->assertEquals($expected, $result);
	}

}
