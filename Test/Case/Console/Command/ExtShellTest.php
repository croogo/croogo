<?php
App::uses('ShellDispatcher', 'Console');
App::uses('AppShell', 'Console/Command');
App::uses('Shell', 'Console');
App::uses('ExtShell', 'Console/Command');
App::uses('Folder', 'Utility');

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
class ExtShellTest extends CakeTestCase {
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
		'app.role',
		'app.setting',
		'app.taxonomy',
		'app.term',
		'app.type',
		'app.types_vocabulary',
		'app.user',
		'app.vocabulary',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		App::build(array(
			'Plugin' => array(TESTS . 'test_app' . DS . 'Plugin' . DS),
			'View' => array(TESTS . 'test_app' . DS . 'View' . DS),
		), App::PREPEND);
		$Folder = new Folder(APP . 'Plugin' . DS . 'Example');
		$Folder->copy(TESTS . 'test_app' . DS . 'Plugin' . DS . 'Example');
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
		$Setting = ClassRegistry::init('Setting');
		$Link = ClassRegistry::init('Link');
		$Shell = new ExtShell();

		$Shell->args = array('deactivate', 'plugin', 'Example');
		$Shell->main();
		$result = $Setting->findByKey('Hook.bootstraps');
		$this->assertFalse(in_array('example', explode(',', $result['Setting']['value'])));
		$result = $Link->findByTitle('Example');
		$this->assertFalse(!empty($result));

		$Shell->args = array('activate', 'plugin', 'Example');
		$Shell->main();
		$result = $Setting->findByKey('Hook.bootstraps');
		$this->assertTrue(in_array('example', explode(',', $result['Setting']['value'])));
		$result = $Link->findByTitle('Example');
		$this->assertTrue(!empty($result));
	}

/**
 * testTheme
 *
 * @return void
 */
	public function testTheme() {
		$Setting = ClassRegistry::init('Setting');

		$Shell = new ExtShell();
		$Shell->args = array('activate', 'theme', 'minimal');
		$Shell->main();
		$result = $Setting->findByKey('Site.theme');
		$this->assertEquals('minimal', $result['Setting']['value']);

		$Shell = new ExtShell();
		$Shell->args = array('deactivate', 'theme');
		$Shell->main();
		$result = $Setting->findByKey('Site.theme');
		$this->assertEquals('', $result['Setting']['value']);

		$Shell = new ExtShell();
		$Shell->args = array('activate', 'theme', 'minimal');
		$Shell->main();
		$Shell->args = array('activate', 'theme', 'default');
		$Shell->main();
		$result = $Setting->findByKey('Site.theme');
		$this->assertEquals('', $result['Setting']['value']);
	}
}
