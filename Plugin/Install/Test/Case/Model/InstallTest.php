<?php
App::uses('CroogoTestCase', 'TestSuite');
App::uses('MigrationVersion', 'Migrations.Lib');

class InstallTest extends CroogoTestCase {

	public function setUp() {
		parent::setUp();
		$this->Install = ClassRegistry::init('Install');
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function testRunMigrationsOk() {
		$croogoPlugin = $this->getMock('CroogoPlugin');
		$croogoPlugin->expects($this->any())
				->method('migrate')
				->will($this->returnValue(true));
		$this->Install->setCroogoPlugin($croogoPlugin);
		$this->assertEquals(true, $this->Install->runMigrations());
	}
	
	public function testRunMigrationsKo() {
		$croogoPlugin = $this->getMock('CroogoPlugin');
		$croogoPlugin->expects($this->any())
				->method('migrate')
				->will($this->returnValue(false));
		$this->Install->setCroogoPlugin($croogoPlugin);
		$this->assertEquals(false, $this->Install->runMigrations());
	}
}

?>
