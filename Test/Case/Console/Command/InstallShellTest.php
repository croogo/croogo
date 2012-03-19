<?php
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('InstallShell', 'Console/Command');
App::uses('Folder', 'Utility');

/**
 * TestInstallShell class
 */
class TestInstallShell extends InstallShell {
/**
 * Open _githubUrl for testing
 *
 * @param string $url
 * @return string
 */
	public function githubUrl($url = null) {
		return $this->_githubUrl($url);
	}
}

/**
 * Install Shell Test
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
class InstallShellTest extends CakeTestCase {
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
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		$Folder = new Folder(TMP);
		$files = $Folder->find('croogo_.*');
		foreach ($files as $file) {
			unlink(TMP . $file);
		}
		$Folder = new Folder(TESTS . 'test_app' . DS . 'Plugin' . DS . 'Example');
		$Folder->delete();
	}

/**
 * testMain
 *
 * @return void
 */
	public function testMain() {
		$Shell = $this->getMock('InstallShell', array('_shell_exec', 'dispatchShell'));
		$Shell->expects($this->once())
			->method('_shell_exec')
			->will($this->returnCallback(array($this, 'callbackDownloadFile')));
		$Shell->expects($this->once())
			->method('dispatchShell')
			->with(array('ext', 'activate', 'plugin', 'Example'))
			->will($this->returnValue(true));
		$Shell->args = array('plugin', 'shama', 'croogo');
		$Shell->main();
	}

/**
 * testGithubUrl
 */
	public function testGithubUrl() {
		$Shell = new TestInstallShell();

		$expected = 'https://github.com/shama/test/zipball/master';

		$result = $Shell->githubUrl('https://github.com/shama/test/');
		$this->assertEquals($expected, $result);

		$result = $Shell->githubUrl('https://github.com/shama/test.git');
		$this->assertEquals($expected, $result);

		$result = $Shell->githubUrl('git://github.com/shama/test.git');
		$this->assertEquals($expected, $result);
	}

/**
 * Called when we want to pretend to download a file
 */
	public function callbackDownloadFile() {
		preg_match('/ -o (.+)/', func_get_arg(0), $zip);
		$dest = $zip[1];
		$src = CakePlugin::path('Extensions') . 'Test' . DS . 'test_files' . DS . 'example_plugin.zip';
		copy($src, $dest);
		return 'Here is that thing you wanted';
	}
}
