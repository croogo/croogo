<?php
App::uses('CroogoTestCase', 'TestSuite');
App::uses('MigrationVersion', 'Migrations.Lib');

class InstallTest extends CroogoTestCase {

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

	protected function _runProtectedMethod($name, $args = array()) {
		$method = new ReflectionMethod(get_class($this->Install), $name);
		$method->setAccessible(true);
		return $method->invokeArgs($this->Install, $args);
	}
}