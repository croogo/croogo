<?php
App::uses('Setting', 'Settings.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class SettingTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.users.aco',
		'plugin.users.aro',
		'plugin.users.aros_aco',
		'plugin.blocks.block',
		'plugin.comments.comment',
		'plugin.contacts.contact',
		'plugin.translate.i18n',
		'plugin.settings.language',
		'plugin.menus.link',
		'plugin.menus.menu',
		'plugin.contacts.message',
		'plugin.meta.meta',
		'plugin.nodes.node',
		'plugin.taxonomy.nodes_taxonomy',
		'plugin.blocks.region',
		'plugin.users.role',
		'plugin.settings.setting',
		'plugin.taxonomy.taxonomy',
		'plugin.taxonomy.term',
		'plugin.taxonomy.type',
		'plugin.taxonomy.types_vocabulary',
		'plugin.users.user',
		'plugin.taxonomy.vocabulary',
	);

	public function setUp() {
		parent::setUp();
		$this->Setting = ClassRegistry::init('Settings.Setting');
		$this->Setting->settingsPath = TESTS . 'test_app' . DS . 'Config' . DS . 'settings.json';
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
		$this->assertEqual(Configure::read('Site.title'), 'Croogo - Test');
	}
}
