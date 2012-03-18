<?php
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('ExtShell', 'Console/Command');

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
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		App::build(array(
			'Plugin' => array(TESTS . 'test_app' . DS . 'Plugin' . DS),
		), App::PREPEND);
	}

/**
 * testPlugin
 *
 * @return void
 */
	public function testPlugin() {
		$this->skipIf(true, 'Skipping ExtShell tests until CroogoComponent functionality is moved to a Lib.');
		$Shell = new ExtShell();
		$Shell->args = array('plugin', 'example');
		$Shell->main();
	}

/**
 * testTheme
 *
 * @return void
 */
	public function testTheme() {
		$Shell = new ExtShell();
		$Shell->args = array('theme', 'minimal');
		$Shell->main();
		$result = $Shell->Croogo->controller->Setting->findByKey('Site.theme');
		$this->assertEquals('minimal', $result['Setting']['value']);

		$Shell = new ExtShell();
		$Shell->args = array('deactivate', 'theme');
		$Shell->main();
		$result = $Shell->Croogo->controller->Setting->findByKey('Site.theme');
		$this->assertEquals('', $result['Setting']['value']);
	}
}
