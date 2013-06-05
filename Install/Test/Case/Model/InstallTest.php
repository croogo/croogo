<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');
App::uses('MigrationVersion', 'Migrations.Lib');
App::uses('User', 'Users.Model');

class InstallTest extends CroogoTestCase {

	public $fixtures = array(
		'aro',
		'plugin.install.user',
		'plugin.install.role',
	);

	public function setUp() {
		parent::setUp();

		CakePlugin::load('Install');
		$this->Install = ClassRegistry::init('Install.Install');
	}

	public function testRunMigrationsOk() {
		$croogoPlugin = $this->getMock('CroogoPlugin');
		$croogoPlugin->expects($this->any())
				->method('migrate')
				->will($this->returnValue(true));
		$this->_runProtectedMethod('_setCroogoPlugin', array($croogoPlugin));
		$this->assertEquals(true, $this->Install->runMigrations('Users'));
	}

	public function testRunMigrationsKo() {
		$croogoPlugin = $this->getMock('CroogoPlugin');
		$croogoPlugin->expects($this->any())
				->method('migrate')
				->will($this->returnValue(false));
		$this->_runProtectedMethod('_setCroogoPlugin', array($croogoPlugin));
		$this->assertEquals(false, $this->Install->runMigrations('Users'));
	}

	public function testSetDatabaseMigrationError() {
		$croogoPlugin = $this->getMock('CroogoPlugin');
		$croogoPlugin->expects($this->any())
			->method('migrate')
			->will($this->returnValue(false));
		$this->_runProtectedMethod('_setCroogoPlugin', array($croogoPlugin));
		$this->assertEquals(false, $this->Install->setDatabase());
	}

	public function testAddAdminUserOk() {
		$user = array('User' => array(
			'username' => 'admin',
			'password' => '123456',
		));
		$this->Install->addAdminUser($user);
		$count = ClassRegistry::init('Users.User')->find('count');
		$this->assertEqual($count, 1);
	}

	public function testAddAdminUserBadPassword() {
		$user = array('User' => array(
			'username' => 'admin',
			'password' => '1234',
		));
		$this->Install->addAdminUser($user);
		$count = ClassRegistry::init('Users.User')->find('count');
		$this->assertEqual($count, 0);
	}

	protected function _runProtectedMethod($name, $args = array()) {
		$method = new ReflectionMethod(get_class($this->Install), $name);
		$method->setAccessible(true);
		return $method->invokeArgs($this->Install, $args);
	}
}