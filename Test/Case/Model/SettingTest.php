<?php
App::uses('Setting', 'Model');
App::uses('CroogoTestCase', 'TestSuite');

class SettingTest extends CroogoTestCase {

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
		$this->Setting->settingsPath = TESTS . 'test_app' . DS . 'Config' . DS . 'settings.yml';
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
		$this->Setting->write('Site.title', 'My new site title', array('editable' => 1));
		$siteTitle = $this->Setting->findByKey('Site.title');
		$this->assertEquals('My new site title', $siteTitle['Setting']['value']);

		$this->Setting->write('Site.title', 'My new site title', array('input_type' => 'checkbox'));
		$siteTitle = $this->Setting->findByKey('Site.title');
		$this->assertTrue($siteTitle['Setting']['editable']);

		$this->Setting->write('Site.title', 'My new site title', array('input_type' => 'textarea', 'editable' => false));
		$siteTitle = $this->Setting->findByKey('Site.title');
		$this->assertEquals('textarea', $siteTitle['Setting']['input_type']);
		$this->assertFalse($siteTitle['Setting']['editable']);
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
