<?php
App::uses('ShellDispatcher', 'Console');
App::uses('AppShell', 'Console/Command');
App::uses('Shell', 'Console');
App::uses('ExtShell', 'Console/Command');
App::uses('Folder', 'Utility');
App::uses('CroogoTestCase', 'TestSuite');

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
		'app.comment',
		'app.menu',
		'app.block',
		'app.link',
		'app.meta',
		'app.node',
		'app.nodes_taxonomy',
		'app.region',
		'app.role',
		'app.setting',
		'app.taxonomy',
		'app.term',
		'app.type',
		'app.types_vocabulary',
		'app.user',
		'app.vocabulary',
		'app.aro',
		'app.aco',
		'app.aros_aco',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$Folder = new Folder(APP . 'Plugin' . DS . 'Example');
		$Folder->copy(TESTS . 'test_app' . DS . 'Plugin' . DS . 'Example');
		$this->Setting = ClassRegistry::init('Setting');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		$Folder = new Folder(TESTS . 'test_app' . DS . 'Plugin' . DS . 'Example');
		$Folder->delete();
	}

/**
 * testPlugin
 *
 * @return void
 */
	public function testPlugin() {
		$Link = ClassRegistry::init('Link');
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
		$this->assertEquals('', $result['Setting']['value']);

		$Shell->args = array('activate', 'theme', 'Mytheme');
		$Shell->main();
		$Shell->args = array('activate', 'theme', 'default');
		$Shell->main();
		$result = $this->Setting->findByKey('Site.theme');
		$this->assertEquals('', $result['Setting']['value']);
	}
}
