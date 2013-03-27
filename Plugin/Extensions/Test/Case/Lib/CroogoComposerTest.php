<?php

App::uses('CroogoComposer', 'Extensions.Lib');
App::uses('CroogoTestCase', 'Croogo.Lib/TestSuite');

/**
 * Croogo Composer Test
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
class CroogoComposerTest extends CroogoTestCase {

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
		$this->CroogoComposer = new CroogoComposer();
		$this->CroogoComposer->appPath = CakePlugin::path('Extensions') . 'Test' . DS . 'test_app' . DS;
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
		$File = new File(CakePlugin::path('Extensions') . 'Test' . DS . 'test_app' . DS . 'composer.json');
		$File->delete();
	}

/**
 * testGetComposer
 *
 * @return void
 */
	public function testGetComposer() {
		$CroogoComposer = $this->getMock('CroogoComposer', array('_shellExec'));
		$CroogoComposer->appPath = $this->CroogoComposer->appPath;
		$CroogoComposer->expects($this->any())
			->method('_shellExec')
			->with(
				$this->equalTo('curl -s http://getcomposer.org/installer | php -- --install-dir=' . $CroogoComposer->appPath)
			)
			->will($this->returnValue(true));
		$CroogoComposer->getComposer();
	}

/**
 * testRunComposer
 *
 * @return void
 */
	public function testRunComposer() {
		$CroogoComposer = $this->getMock('CroogoComposer', array('_shellExec'));
		$CroogoComposer->appPath = $this->CroogoComposer->appPath;
		$CroogoComposer->getComposer();
		$CroogoComposer->expects($this->once())
			->method('_shellExec')
			->with(
				$this->equalTo('php ' . $CroogoComposer->composerPath . ' install')
			)
			->will($this->returnValue(true));
		$CroogoComposer->runComposer();
	}

/**
 * testSetConfig
 *
 * @return void
 */
	public function testSetConfig() {
		$result = $this->CroogoComposer->setConfig(array(
			'shama/ftp' => '*',
		));
		$this->assertTrue($result);
		$File = new File($this->CroogoComposer->appPath . 'composer.json');
		$result = $File->read();
		$File->close();
		$expected = <<<END
{
\s+"minimum-stability": "dev",
\s+"config": {
\s+"vendor-dir": "Vendor",
\s+"bin-dir": "Vendor/bin"
\s+},
\s+"require": {
\s+"composer/installers": "\*",
\s+"shama/ftp": "\*"
\s+}
}
END;
		$this->assertRegExp($expected, trim($result));
	}
}