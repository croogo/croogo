<?php
namespace Croogo\Install\Test\TestCase\Model;
App::uses('CroogoTestCase', 'Croogo.TestSuite');
App::uses('MigrationVersion', 'Migrations.Lib');
App::uses('User', 'Users.Model');

class InstallTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.users.aro',
		'plugin.install.install_user',
		'plugin.install.install_role',
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

	public function testRunMigrationsFailed() {
		$croogoPlugin = $this->getMock('CroogoPlugin');
		$croogoPlugin->expects($this->any())
				->method('migrate')
				->will($this->returnValue(false));
		$this->_runProtectedMethod('_setCroogoPlugin', array($croogoPlugin));
		$this->assertEquals(false, $this->Install->runMigrations('Users'));
	}

	public function testAddAdminUserOk() {
		$user = array('User' => array(
			'username' => 'admin',
			'password' => '123456',
		));
		$this->Install->addAdminUser($user);
		$User = ClassRegistry::init('Users.User');

		$count = $User->find('count');
		$this->assertEqual($count, 1);

		$saved = $User->findByUsername('admin');
		$expected = AuthComponent::password($user['User']['password']);
		$this->assertEqual($expected, $saved['User']['password'], 'Password mismatch');
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
		$this->skipIf(version_compare(PHP_VERSION, '5.3.0', '<'), 'PHP >= 5.3.0 required to run this test.');
		$method = new ReflectionMethod(get_class($this->Install), $name);
		$method->setAccessible(true);
		return $method->invokeArgs($this->Install, $args);
	}
}