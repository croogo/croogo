<?php
App::uses('ShellDispatcher', 'Console');
App::uses('AppShell', 'Console/Command');
App::uses('Shell', 'Console');
App::uses('InstallShell', 'Croogo.Console/Command');
App::uses('Folder', 'Utility');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

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
 * fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.settings.setting',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		App::build(array(
			'Plugin' => array(CakePlugin::path('Croogo') . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS),
			'View' => array(CakePlugin::path('Croogo') . 'Test' . DS . 'test_app' . DS . 'View' . DS),
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
		$Folder = new Folder(CakePlugin::path('Croogo') . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS . 'Example');
		$Folder->delete();
		$Folder = new Folder(CakePlugin::path('Croogo') . 'Test' . DS . 'test_app' . DS . 'View' . DS . 'Themed' . DS . 'Minimal');
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
			->with(
				$this->equalTo('ext'),
				$this->equalTo('activate'),
				$this->equalTo('plugin'),
				$this->equalTo('Example')
			)
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
			->with(
				$this->equalTo('ext'),
				$this->equalTo('activate'),
				$this->equalTo('theme'),
				$this->equalTo('Minimal')
			)
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
 * testComposerInstall
 */
	public function testComposerInstall() {
		$this->skipIf(version_compare(PHP_VERSION, '5.3.0', '<'), 'PHP >= 5.3.0 required to run this test.');

		$Shell = new ReflectionClass('InstallShell');
		$prop = $Shell->getProperty('_ExtensionsInstaller');
		$prop->setAccessible(true);
		$ShellMock = $this->getMock('InstallShell', array('dispatchShell', 'out', 'err'));

		$ExtensionsInstaller = $this->getMock('ExtensionsInstaller', array('composerInstall'));
		$prop->setValue($ShellMock, $ExtensionsInstaller);

		$ExtensionsInstaller->expects($this->once())
			->method('composerInstall')
			->with(
				$this->equalTo(array(
					'package' => 'shama/ftp',
					'version' => '1.1.1',
					'type' => 'plugin',
				))
			)
			->will($this->returnValue(array('returnValue' => 0)));

		$prop = $Shell->getProperty('_CroogoPlugin');
		$prop->setAccessible(true);
		$CroogoPlugin = $this->getMock('CroogoPlugin');
		$prop->setValue($ShellMock, $CroogoPlugin);

		$CroogoPlugin->expects($this->once())
			->method('getData')
			->will($this->returnValue(true));

		$ShellMock->expects($this->once())
			->method('dispatchShell')
			->with(
				$this->equalTo('ext'),
				$this->equalTo('activate'),
				$this->equalTo('plugin'),
				$this->equalTo('Ftp'),
				$this->equalTo('--quiet')
			)
			->will($this->returnValue(true));

		$ShellMock->args = array('plugin', 'shama/ftp', '1.1.1');
		$ShellMock->main();
	}

/**
 * Called when we want to pretend to download a plugin
 */
	public function callbackDownloadPlugin() {
		$argOne = func_get_arg(0);
		preg_match('/ -o (.+) /', $argOne, $zip);
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
		preg_match('/ -o (.+) /', $argOne, $zip);
		$dest = $zip[1];
		$src = CakePlugin::path('Extensions') . 'Test' . DS . 'test_files' . DS . 'example_theme.zip';
		copy($src, $dest);
		return 'Here is that thing you wanted';
	}
}
