<?php

namespace Croogo\Extensions\Test\TestCase;

use Cake\Utility\Folder;
use Croogo\Lib\TestSuite\CroogoTestCase;
use Extensions\Lib\ExtensionsInstaller;
/**
 * Extensions Installer Test
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
		$path = Plugin::path('Extensions') . 'Test' . DS;
		App::build(array(
			'Plugin' => array($path . 'test_app' . DS . 'Plugin' . DS),
			'View' => array($path . 'test_app' . DS . 'View' . DS),
		), App::PREPEND);
		$this->testPlugin = $path . 'test_files' . DS . 'example_plugin.zip';
		$this->minimalPlugin = $path . 'test_files' . DS . 'minimal_plugin.zip';
		$this->invalidPlugin = $path . 'test_files' . DS . 'invalid_plugin.zip';
		$this->testTheme = $path . 'test_files' . DS . 'example_theme.zip';
		$this->ExtensionsInstaller = new ExtensionsInstaller();
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		$path = Plugin::path('Extensions') . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS . 'Example';
		$Folder = new Folder($path);
		$Folder->delete();
		$path = Plugin::path('Extensions') . 'Test' . DS . 'test_app' . DS . 'View' . DS . 'Themed' . DS . 'Minimal';
		$Folder = new Folder($path);
		$Folder->delete();
		if (file_exists($this->minimalPlugin)) {
			unlink($this->minimalPlugin);
		}
		if (file_exists($this->invalidPlugin)) {
			unlink($this->invalidPlugin);
		}
	}

/**
 * Helper method to create test zip file
 */
	protected function _addDirectoryToZip($zip, $dir, $base) {
		$newFolder = str_replace($base, '', $dir);
		$zip->addEmptyDir($newFolder);
		foreach (glob($dir . '/*') as $file) {
			if (is_dir($file)) {
				$zip = $this->_addDirectoryToZip($zip, $file, $base);
			} else {
				$newFile = str_replace($base, '', $file);
				$zip->addFile($file, $newFile);
			}
		}
		return $zip;
	}

/**
 * Create a test zip file $zipPath from $dirName
 */
	protected function _createZip($zipPath, $dirName) {
		$dir = Plugin::path('Extensions') . 'Test' . DS . 'test_files' . DS;
		chdir($dir);
		$zip = new ZipArchive();
		$zip->open($zipPath, ZipArchive::OVERWRITE);
		$this->_addDirectoryToZip($zip, $dirName, $dir);
		$zip->close();
		$this->assertTrue(file_exists($zipPath), 'Test zip not created');
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
 * testGetPluginName
 *
 * @return void
 */
	public function testGetPluginNameMinimal() {
		$this->_createZip($this->minimalPlugin, 'Minimal');
		$result = $this->ExtensionsInstaller->getPluginName($this->minimalPlugin);
		$this->assertEquals('Minimal', $result);
	}

/**
 * testGetPluginNameInvalid
 *
 * @return void
 * @expectedException CakeException
 */
	public function testGetPluginNameInvalid() {
		$this->_createZip($this->invalidPlugin, 'Invalid');
		$result = $this->ExtensionsInstaller->getPluginName($this->invalidPlugin);
	}

/**
 * testExtractPlugin
 */
	public function testExtractPlugin() {
		$result = $this->ExtensionsInstaller->extractPlugin($this->testPlugin);
		$this->assertTrue($result);
		$path = Plugin::path('Extensions') . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS . 'Example' . DS;
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
		$path = Plugin::path('Extensions') . 'Test' . DS . 'test_app' . DS . 'View' . DS . 'Themed' . DS . 'Minimal' . DS;
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