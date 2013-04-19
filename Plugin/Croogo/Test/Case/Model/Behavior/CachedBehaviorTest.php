<?php
App::uses('User', 'Users.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class CachedBehaviorTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.users.role',
		'plugin.users.user',
	);

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->User = ClassRegistry::init('Users.User');
		$this->User->Behaviors->unload('Acl');
		$this->User->Behaviors->unload('Searchable');
		$this->defaultPrefix = Configure::read('Cache.defaultPrefix');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->User);
		ClassRegistry::flush();
	}

/**
 * testClearCache
 */
	public function testClearCache() {
		$cacheName = 'users';
		Cache::delete($cacheName . '_eng', 'users_login');
		$this->User->useCache = true;
		$this->User->Behaviors->load('Croogo.Cached', array(
			'config' => 'users_login',
			'prefix' => array(
				'users_',
			),
		));

		// find() causes cache file to be created
		$users = $this->User->find('list', array(
			'cache' => array(
				'name' => $cacheName,
				'config' => 'users_login',
				'prefix' => '',
			),
		));

		$cacheFile = CACHE . 'queries' . DS . $this->defaultPrefix . $cacheName . '_' . Configure::read('Config.language');
		$fileExists = file_exists($cacheFile);
		$this->assertEquals(true, $fileExists);
		$this->assertEquals(3, count($users));
		$this->assertNotEmpty($users);

		// delete() should delete/invalidate the cache
		$this->User->id = 2;
		$this->User->delete();
		$fileExists = file_exists($cacheFile);
		$this->assertEquals(false, $fileExists);

		// find() should recreate the cache file with the correct user count
		$users = $this->User->find('list', array(
			'cache' => array(
				'name' => $cacheName,
				'config' => 'users_login',
				'prefix' => '',
			),
		));
		$this->assertEquals(2, count($users));
		$fileExists = file_exists($cacheFile);
		$this->assertEquals(true, $fileExists);
	}

}
