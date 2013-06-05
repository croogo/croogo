<?php

App::uses('CroogoTestCase', 'Croogo.Lib/TestSuite');
App::uses('ExtensionsInstaller', 'Extensions.Lib');
App::uses('Folder', 'Utility');

/**
 * Extensions Installer Test
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
class ExtensionsInstallerTest extends CroogoTestCase {

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		App::build(array(
			'Plugin' => array(CakePlugin::path('Extensions') . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS),
			'View' => array(CakePlugin::path('Extensions') . 'Test' . DS . 'test_app' . DS . 'View' . DS),
		), App::PREPEND);
		$this->testPlugin = CakePlugin::path('Extensions') . 'Test' . DS . 'test_files' . DS . 'example_plugin.zip';
		$this->testTheme = CakePlugin::path('Extensions') . 'Test' . DS . 'test_files' . DS . 'example_theme.zip';
		$this->ExtensionsInstaller = new ExtensionsInstaller();
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		$path = CakePlugin::path('Extensions') . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS . 'Example';
		$Folder = new Folder($path);
		$Folder->delete();
		$path = CakePlugin::path('Extensions') . 'Test' . DS . 'test_app' . DS . 'View' . DS . 'Themed' . DS . 'Minimal';
		$Folder = new Folder($path);
		$Folder->delete();
	}

/**
 * testGetPluginName
 *
 * @return void
 */
	public function testGetPluginName() {
		$result = $this->ExtensionsInstaller->getPluginName($this->testPlugin);
		$this->assertEquals('Example', $result);
	}

/**
 * testExtractPlugin
 */
	public function testExtractPlugin() {
		$result = $this->ExtensionsInstaller->extractPlugin($this->testPlugin);
		$this->assertTrue($result);
		$path = CakePlugin::path('Extensions') . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS . 'Example' . DS;
		$Folder = new Folder($path);
		$files = $Folder->findRecursive();
		foreach ($files as $key => $file) {
			$files[$key] = str_replace($path, '', $file);
		}
		$this->assertTrue(in_array('Config' . DS . 'ExampleActivation.php', $files));
		$this->assertTrue(in_array('Model' . DS . 'ExampleAppModel.php', $files));
		$this->assertTrue(in_array('Controller' . DS . 'ExampleAppController.php', $files));
	}

/**
 * testGetThemeName
 */
	public function testGetThemeName() {
		$result = $this->ExtensionsInstaller->getThemeName($this->testTheme);
		$this->assertEquals('Minimal', $result);
	}

/**
 * testExtractTheme
 */
	public function testExtractTheme() {
		$result = $this->ExtensionsInstaller->extractTheme($this->testTheme);
		$this->assertTrue($result);
		$path = CakePlugin::path('Extensions') . 'Test' . DS . 'test_app' . DS . 'View' . DS . 'Themed' . DS . 'Minimal' . DS;
		$Folder = new Folder($path);
		$files = $Folder->findRecursive();
		foreach ($files as $key => $file) {
			$files[$key] = str_replace($path, '', $file);
		}
		$this->assertTrue(in_array('Layouts' . DS . 'default.ctp', $files));
		$this->assertTrue(in_array('webroot' . DS . 'theme.json', $files));
		$this->assertTrue(in_array('webroot' . DS . 'css' . DS . 'theme.css', $files));
		$this->assertTrue(in_array('webroot' . DS . 'img' . DS . 'screenshot.png', $files));
	}

/**
 * testComposerInstall
 *
 * @expectedException CakeException
 */
	public function testComposerInstall() {
		$this->skipIf(version_compare(PHP_VERSION, '5.3.0', '<'), 'PHP >= 5.3.0 required to run this test.');

		$ExtensionsInstaller = new ReflectionClass('ExtensionsInstaller');
		$prop = $ExtensionsInstaller->getProperty('_CroogoComposer');
		$prop->setAccessible(true);
		$ExtensionsInstallerMock = new ExtensionsInstaller();

		$CroogoComposer = $this->getMock('CroogoComposer', array(
			'getComposer', 'setConfig', 'runComposer',
		));
		$prop->setValue($ExtensionsInstallerMock, $CroogoComposer);

		$CroogoComposer->expects($this->once())
			->method('getComposer')
			->will($this->returnValue(true));
		$CroogoComposer->expects($this->once())
			->method('setConfig')
			->with(
				$this->equalTo(array('shama/ftp' => '*'))
			)
			->will($this->returnValue(true));
		$CroogoComposer->expects($this->once())
			->method('runComposer')
			->will($this->returnValue(true));

		$ExtensionsInstallerMock->composerInstall(array(
			'package' => 'shama/ftp',
		));

		$ExtensionsInstallerMock->composerInstall(array(
			'package' => 'nothemes/yet',
			'type' => 'theme',
		));
	}
}