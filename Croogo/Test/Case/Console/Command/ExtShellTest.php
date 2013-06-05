<?php
App::uses('ShellDispatcher', 'Console');
App::uses('AppShell', 'Console/Command');
App::uses('Shell', 'Console');
App::uses('ExtShell', 'Croogo.Console/Command');
App::uses('Folder', 'Utility');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

/**
 * Ext Shell Test
 *
 * PHP version 5
 *
 * @category Test
 * @package  Croogo
 * @version  1.4
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExtShellTest extends CroogoTestCase {

/**
 * fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.comments.comment',
		'plugin.menus.menu',
		'plugin.blocks.block',
		'plugin.menus.link',
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
		'plugin.users.aro',
		'plugin.users.aco',
		'plugin.users.aros_aco',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$Folder = new Folder(APP . 'Plugin' . DS . 'Example');
		$Folder->copy(CakePlugin::path('Croogo') . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS . 'Example');
		$this->Setting = ClassRegistry::init('Settings.Setting');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		$Folder = new Folder(CakePlugin::path('Croogo') . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS . 'Example');
		$Folder->delete();
	}

/**
 * testPlugin
 *
 * @return void
 */
	public function testPlugin() {
		$Link = ClassRegistry::init('Menus.Link');
		$Shell = $this->getMock('ExtShell', array('out', 'err'));

		$Shell->args = array('deactivate', 'plugin', 'Example');
		$Shell->main();
		$result = $this->Setting->findByKey('Hook.bootstraps');
		$this->assertFalse(in_array('Example', explode(',', $result['Setting']['value'])));
		$result = $Link->findByTitle('Example');
		$this->assertFalse(!empty($result));

		$Shell->args = array('activate', 'plugin', 'Example');
		$Shell->main();
		$result = $this->Setting->findByKey('Hook.bootstraps');
		$this->assertTrue(in_array('Example', explode(',', $result['Setting']['value'])));
		$result = $Link->findByTitle('Example');
		$this->assertTrue(!empty($result));

		$bogusPlugin = 'Bogus';
		$Shell->args = array('activate', 'plugin', $bogusPlugin);
		$Shell->main();
		$result = $this->Setting->findByKey('Hook.bootstraps');
		$this->assertFalse(in_array($bogusPlugin, explode(',', $result['Setting']['value'])));
	}

/**
 * testForceActivation
 */
	public function testForceActivation() {
		$Shell = $this->getMock('ExtShell', array('out', 'err'));

		$Shell->args = array('activate', 'plugin', 'TestPlugin');
		$Shell->main();
		$result = $this->Setting->findByKey('Hook.bootstraps');
		$this->assertFalse(in_array('TestPlugin', explode(',', $result['Setting']['value'])));

		$Shell->args = array('activate', 'plugin', 'TestPlugin');
		$Shell->params = array('force' => true);
		$Shell->main();
		$result = $this->Setting->findByKey('Hook.bootstraps');
		$this->assertTrue(in_array('TestPlugin', explode(',', $result['Setting']['value'])));
	}

/**
 * testForceDeactivation
 */
	public function testForceDeactivation() {
		$Shell = $this->getMock('ExtShell', array('out', 'err'));

		$result = $this->Setting->findByKey('Hook.bootstraps');
		$bogus = $result['Setting']['value'] . ',Bogus';
		$this->Setting->write('Hook.bootstraps', $bogus);

		$Shell->args = array('deactivate', 'plugin', 'Bogus');
		$Shell->params['force'] = true;
		$Shell->main();

		$result = $this->Setting->findByKey('Hook.bootstraps');
		$this->assertFalse(in_array('Bogus', explode(',', $result['Setting']['value'])));
	}

/**
 * testTheme
 *
 * @return void
 */
	public function testTheme() {
		$Shell = $this->getMock('ExtShell', array('out', 'err'));
		$Shell->args = array('activate', 'theme', 'Mytheme');
		$Shell->main();
		$result = $this->Setting->findByKey('Site.theme');
		$this->assertEquals('Mytheme', $result['Setting']['value']);
		$this->assertEquals('Mytheme', Configure::read('Site.theme'));

		$Shell->args = array('activate', 'theme', 'Bogus');
		$Shell->main();
		$result = $this->Setting->findByKey('Site.theme');
		$this->assertEquals('Mytheme', $result['Setting']['value']);
		$this->assertEquals('Mytheme', Configure::read('Site.theme'));

		$Shell->args = array('deactivate', 'theme');
		$Shell->main();
		$result = $this->Setting->findByKey('Site.theme');
		$this->assertEquals('Mytheme', $result['Setting']['value']);

		$Shell->args = array('deactivate', 'theme', 'Mytheme');
		$Shell->main();
		$result = $this->Setting->findByKey('Site.theme');
		$this->assertEquals('Mytheme', $result['Setting']['value']);

		$Shell->args = array('activate', 'theme', 'Mytheme');
		$Shell->main();
		$Shell->args = array('activate', 'theme', 'default');
		$Shell->main();
		$result = $this->Setting->findByKey('Site.theme');
		$this->assertEquals('', $result['Setting']['value']);
	}
}
