<?php
App::uses('ShellDispatcher', 'Console');
App::uses('AppShell', 'Console/Command');
App::uses('Shell', 'Console');
App::uses('InstallShell', 'Console/Command');
App::uses('Folder', 'Utility');
App::uses('CroogoTestCase', 'TestSuite');

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

	public function out($message = null, $newlines = 1, $level = Shell::NORMAL) {
	}

	public function err($message = null, $newlines = 1) {
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
class InstallShellTest extends CroogoTestCase {

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
		$Folder = new Folder(TESTS . 'test_app' . DS . 'View' . DS . 'Themed' . DS . 'Minimal');
		$Folder->delete();
	}

/**
 * testInstallPlugin
 *
 * @return void
 */
	public function testInstallPlugin() {
		$Shell = $this->getMock('InstallShell', array('out', 'err', '_shellExec', 'dispatchShell'));
		$Shell->expects($this->once())
			->method('_shellExec')
			->will($this->returnCallback(array($this, 'callbackDownloadPlugin')));
		$Shell->expects($this->once())
			->method('dispatchShell')
			->with(array('ext', 'activate', 'plugin', 'Example'))
			->will($this->returnValue(true));
		$Shell->args = array('plugin', 'shama', 'croogo');
		$Shell->main();
	}

/**
 * testInstallTheme
 *
 * @return void
 */
	public function testInstallTheme() {
		$Shell = $this->getMock('InstallShell', array('out', 'err', '_shellExec', 'dispatchShell'));
		$Shell->expects($this->once())
			->method('_shellExec')
			->will($this->returnCallback(array($this, 'callbackDownloadTheme')));
		$Shell->expects($this->once())
			->method('dispatchShell')
			->with(array('ext', 'activate', 'theme', 'Minimal'))
			->will($this->returnValue(true));
		$Shell->args = array('theme', 'shama', 'mytheme');
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
 * Called when we want to pretend to download a plugin
 */
	public function callbackDownloadPlugin() {
		$argOne = func_get_arg(0);
		preg_match('/ -o (.+)/', $argOne, $zip);
		$dest = $zip[1];
		$src = CakePlugin::path('Extensions') . 'Test' . DS . 'test_files' . DS . 'example_plugin.zip';
		copy($src, $dest);
		return 'Here is that thing you wanted';
	}

/**
 * Called when we want to pretend to download a theme
 */
	public function callbackDownloadTheme() {
		$argOne = func_get_arg(0);
		preg_match('/ -o (.+)/', $argOne, $zip);
		$dest = $zip[1];
		$src = CakePlugin::path('Extensions') . 'Test' . DS . 'test_files' . DS . 'example_theme.zip';
		copy($src, $dest);
		return 'Here is that thing you wanted';
	}
}
