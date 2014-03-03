<?php
App::uses('Region', 'Blocks.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class RegionTest extends CroogoTestCase {

	public $Region;
	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.blocks.region',
	);

	public function setUp() {
		parent::setUp();
		$this->Region = ClassRegistry::init('Blocks.Region');
	}

	public function tearDown() {
		unset($this->Region);
		parent::tearDown();
	}

	public function testHasModelValidatorLoaded() {
		$this->assertInstanceOf('BlocksAppValidator', $this->Region->validator());
	}
}
