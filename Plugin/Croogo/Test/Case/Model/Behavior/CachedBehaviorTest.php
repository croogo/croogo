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
		$prefixed = $cacheName . '_' . Configure::read('Config.language');
		Cache::delete($cacheName . '_eng', 'users_login');
		$this->User->useCache = true;
		$this->User->Behaviors->load('Croogo.Cached', array(
			'config' => 'users_login',
			'groups' => array('users'),
		));

		// find() causes cache file to be created
		$users = $this->User->find('list', array(
			'cache' => array(
				'name' => $cacheName,
				'config' => 'users_login',
			),
		));

		$cached = Cache::read($prefixed, 'users_login');
		$this->assertEquals(3, count($cached));

		// delete() should delete/invalidate the cache
		$this->User->id = 2;
		$this->User->delete();
		$cached = Cache::read($prefixed, 'users_login');
		$this->assertFalse($cached);

		// find() should recreate the cache file with the correct user count
		$users = $this->User->find('list', array(
			'cache' => array(
				'name' => $cacheName,
				'config' => 'users_login',
				'prefix' => '',
			),
		));
		$this->assertEquals(2, count($users));
		$cached = Cache::read($prefixed, 'users_login');
		$this->assertEquals(2, count($cached));
	}

}
