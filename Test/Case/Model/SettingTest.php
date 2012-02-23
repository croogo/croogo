<?php
// TODO: These tests fire a permission error but should be done in a separate
// test_app and skip if no permissions
App::uses('Setting', 'Model');
class SettingTest extends CakeTestCase {

	public $fixtures = array(
		'aco',
		'aro',
		'aros_aco',
		'block',
		'comment',
		'contact',
		'i18n',
		'language',
		'link',
		'menu',
		'message',
		'meta',
		'node',
		'nodes_taxonomy',
		'region',
		'role',
		'setting',
		'taxonomy',
		'term',
		'type',
		'types_vocabulary',
		'user',
		'vocabulary',
	);
	
	public function setUp() {
		parent::setUp();
		$this->Setting = ClassRegistry::init('Setting');
	}
	
	public function tearDown() {
		parent::tearDown();
		unset($this->Setting);
	}

	public function testWriteNew() {
		$this->Setting->write('Prefix.key', 'value');
		$prefixAnything = $this->Setting->findByKey('Prefix.key');
		$this->assertEqual('value', $prefixAnything['Setting']['value']);
	}

	public function testWriteUpdate() {
		$this->Setting->write('Site.title', 'My new site title');
		$siteTitle = $this->Setting->findByKey('Site.title');
		$this->assertEqual('My new site title', $siteTitle['Setting']['value']);
	}

	public function testDeleteKey() {
		$this->Setting->write('Prefix.key', 'value');
		$this->Setting->deleteKey('Prefix.key');
		$hasAny = $this->Setting->hasAny(array(
			'Setting.key' => 'Prefix.key',
		));
		$this->assertFalse($hasAny);
	}

	public function testWriteConfiguration() {
		$this->Setting->writeConfiguration();
		$this->assertEqual(Configure::read('Site.title'), 'Croogo');
	}
}
