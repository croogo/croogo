<?php
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
class ExtensionsInstallerTest extends CakeTestCase {
/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		App::build(array(
			'Plugin' => array(CakePlugin::path('Extensions') . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS),
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
}