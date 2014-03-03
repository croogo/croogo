<?php
App::uses('Block', 'Blocks.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class BlockTest extends CroogoTestCase {

	public $Block;
	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.blocks.block',
	);

	public function setUp() {
		parent::setUp();
		$this->Block = ClassRegistry::init('Blocks.Block');
	}

	public function tearDown() {
		unset($this->Block);
		parent::tearDown();
	}

	public function testHasModelValidatorLoaded() {
		$this->assertInstanceOf('BlockValidator', $this->Block->validator());
	}
}
