<?php

App::uses('MigrationVersion', 'Migrations.Lib');
App::uses('CroogoPlugin', 'Extensions.Lib');
App::uses('CroogoTestCase', 'Croogo.Lib/TestSuite');

class CroogoPluginTest extends CroogoTestCase {

/**
 * CroogoPlugin class
 * @var CroogoPlugin
 */
	public $CroogoPlugin;

	public function setUp() {
		parent::setUp();
		App::build(array(
			'Plugin' => array(CakePlugin::path('Extensions') . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS),
		), App::PREPEND);

		$this->CroogoPlugin = $this->getMock('CroogoPlugin', array(
			'_writeSetting',
			'needMigration',
		));

		$this->_mapping = array(
			1346748762 => array(
				'version' => 1346748762,
				'name' => '1346748762_first',
				'class' => 'First',
				'type' => 'app',
				'migrated' => '2012-09-04 10:52:42'
			),
			1346748933 => array(
				'version' => 1346748933,
				'name' => '1346748933_addstatus',
				'class' => 'AddStatus',
				'type' => 'app',
				'migrated' => '2012-09-04 10:55:33'
			)
		);
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->CroogoPlugin);
	}

	protected function _getMockMigrationVersion() {
		return $this->getMockBuilder('MigrationVersion')
			->disableOriginalConstructor()
			->getMock();
	}

	public function testGetDataPluginNotActive() {
		$actives = Configure::read('Hook.bootstraps');
		Configure::write('Hook.bootstraps', '');

		$suppliers = $this->CroogoPlugin->getData('Suppliers');

		$needed = array(
			'name' => 'Suppliers',
			'description' => 'Suppliers plugin',
			'active' => false,
			'needMigration' => false
		);
		$this->assertEquals($needed, $suppliers);

		Configure::write('Hook.bootstraps', $actives);
	}

	public function testGetDataPluginActive() {
		$actives = Configure::read('Hook.bootstraps');
		Configure::write('Hook.bootstraps', 'suppliers');

		$migrationVersion = $this->_getMockMigrationVersion();
		$croogoPlugin = new CroogoPlugin($migrationVersion);

		$suppliers = $croogoPlugin->getData('Suppliers');

		$needed = array(
			'name' => 'Suppliers',
			'description' => 'Suppliers plugin',
			'active' => true,
			'needMigration' => false
		);
		$this->assertEquals($needed, $suppliers);

		Configure::write('Hook.bootstraps', $actives);
	}

	public function testGetDataPluginNotExists() {
		$data = $this->CroogoPlugin->getData('NotARealPlugin');
		$this->assertEquals(false, $data);
	}

	public function testGetDataWithEmptyJson() {
		$data = $this->CroogoPlugin->getData('EmptyJson');
		$this->assertEquals(array(), $data);
	}

	public function testNeedMigrationPluginNotExists() {
		$migrationVersion = $this->_getMockMigrationVersion();
		$migrationVersion->expects($this->any())
			->method('getMapping')
			->will($this->returnValue(false));
		$croogoPlugin = new CroogoPlugin($migrationVersion);
		$this->assertEquals(false, $croogoPlugin->needMigration('Anything', true));
	}

	public function testNeedMigrationPluginNotActive() {
		$croogoPlugin = new CroogoPlugin();
		$this->assertEquals(false, $croogoPlugin->needMigration('Anything', false));
	}

	public function testNeedMigrationPluginNoMigration() {
		$migrationVersion = $this->_getMockMigrationVersion();
		$migrationVersion->expects($this->any())
			->method('getMapping')
			->will($this->returnValue($this->_mapping));
		$migrationVersion->expects($this->any())
			->method('getVersion')
			->will($this->returnValue(1346748933));
		$croogoPlugin = new CroogoPlugin($migrationVersion);
		$this->assertEquals(false, $croogoPlugin->needMigration('app', true));
	}

	public function testNeedMigrationPluginWithMigration() {
		$migrationVersion = $this->_getMockMigrationVersion();
		$migrationVersion->expects($this->any())
			->method('getMapping')
			->will($this->returnValue($this->_mapping));
		$migrationVersion->expects($this->any())
			->method('getVersion')
			->will($this->returnValue(1346748762));
		$croogoPlugin = new CroogoPlugin($migrationVersion);
		$this->assertEquals(true, $croogoPlugin->needMigration('app', true));
	}

	public function testMigratePluginNotNeedMigration() {
		$actives = Configure::read('Hook.bootstraps');
		Configure::write('Hook.bootstraps', 'Suppliers');

		$migrationVersion = $this->_getMockMigrationVersion();
		$migrationVersion->expects($this->any())
			->method('getMapping')
			->will($this->returnValue($this->_mapping));
		$croogoPlugin = new CroogoPlugin($migrationVersion);

		$this->assertEquals(false, $croogoPlugin->migrate('Suppliers'));

		Configure::read('Hook.bootstraps', $actives);
	}

	public function testMigratePluginWithMigration() {
		$actives = Configure::read('Hook.bootstraps');
		Configure::write('Hook.bootstraps', 'Suppliers');

		$migrationVersion = $this->_getMockMigrationVersion();
		$migrationVersion->expects($this->any())
			->method('getMapping')
			->will($this->returnValue($this->_mapping));
		$migrationVersion->expects($this->any())
			->method('run')
			->with($this->logicalAnd($this->arrayHasKey('version'), $this->arrayHasKey('type')))
			->will($this->returnValue(true));

		$croogoPlugin = new CroogoPlugin($migrationVersion);

		$this->assertEquals(true, $croogoPlugin->migrate('Suppliers'));

		Configure::read('Hook.bootstraps', $actives);
	}

	public function testMigratePluginWithMigrationError() {
		$actives = Configure::read('Hook.bootstraps');
		Configure::write('Hook.bootstraps', 'Suppliers');

		$migrationVersion = $this->_getMockMigrationVersion();
		$migrationVersion->expects($this->any())
			->method('getMapping')
			->will($this->returnValue($this->_mapping));
		$migrationVersion->expects($this->any())
			->method('run')
			->will($this->returnValue('An error message'));

		$croogoPlugin = new CroogoPlugin($migrationVersion);

		$expectedErrors = array('An error message');
		$this->assertEquals(false, $croogoPlugin->migrate('Suppliers'));
		$this->assertEquals($expectedErrors, $croogoPlugin->migrationErrors);

		Configure::read('Hook.bootstraps', $actives);
	}

	public function testUnmigrate() {
		$actives = Configure::read('Hook.bootstraps');
		Configure::write('Hook.bootstraps', 'Suppliers');

		$migrationVersion = $this->_getMockMigrationVersion();
		$migrationVersion->expects($this->once())
			->method('getMapping')
			->will($this->returnValue($this->_mapping));
		$migrationVersion->expects($this->once())
			->method('run')
			->with($this->arrayHasKey('version', 'type', 'direction'))
			->will($this->returnValue(true));

		$croogoPlugin = new CroogoPlugin($migrationVersion);

		$this->assertEquals(true, $croogoPlugin->unmigrate('Suppliers'));

		Configure::read('Hook.bootstraps', $actives);
	}

	public function testUnmigrateNoMapping() {
		$actives = Configure::read('Hook.bootstraps');
		Configure::write('Hook.bootstraps', 'Suppliers');

		$migrationVersion = $this->_getMockMigrationVersion();
		$migrationVersion->expects($this->once())
			->method('getMapping')
			->will($this->returnValue(array()));
		$migrationVersion->expects($this->never())
			->method('run')
			->will($this->returnValue(false));

		$croogoPlugin = new CroogoPlugin($migrationVersion);

		$this->assertEquals(false, $croogoPlugin->unmigrate('Suppliers'));

		Configure::read('Hook.bootstraps', $actives);
	}

/**
 * testReorderBootstraps
 */
	public function testReorderBootstraps() {
		$bootstraps = explode(',', 'Settings,Taxonomy,Sites,Example');

		$expected = 'Example is already at the last position';
		$result = $this->CroogoPlugin->move('down', 'Example', $bootstraps);
		$this->assertEquals($expected, $result);

		// core and bundled plugins must not be reordered
		$result = $this->CroogoPlugin->move('up', 'Sites', $bootstraps);
		$this->assertEquals('Sites is already at the first position', $result);

		$bootstraps = explode(',', 'Example,Settings,Taxonomy,Sites');
		$result = $this->CroogoPlugin->move('up', 'Example', $bootstraps);
		$this->assertEquals('Example is already at the first position', $result);
	}

/**
 * testReorderBootstrapsWithDependency
 */
	public function testReorderBootstrapsWithDependency() {
		$bootstraps = explode(',', 'Widgets,Editors');

		$expected = 'Plugin Editors depends on Widgets';
		$result = $this->CroogoPlugin->move('up', 'Editors', $bootstraps);
		$this->assertEquals($expected, $result);

		$expected = 'Plugin Editors depends on Widgets';
		$result = $this->CroogoPlugin->move('down', 'Widgets', $bootstraps);
		$this->assertEquals($expected, $result);
	}

/**
 * testDeleteEmptyPlugin
 * @expectedException InvalidArgumentException
 */
	public function testDeleteEmptyPlugin() {
		$this->CroogoPlugin->delete(null);
	}

}