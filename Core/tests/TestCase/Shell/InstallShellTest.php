<?php

namespace Croogo\Core\Test\TestCase\Shell;

use Cake\Console\Shell;
use Cake\Console\ShellDispatcher;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Croogo\Core\Shell\InstallShell;
use Croogo\Core\TestSuite\CroogoTestCase;
use ReflectionClass;

/**
 * TestInstallShell class
 */
class TestInstallShell extends InstallShell
{

/**
 * Open _githubUrl for testing
 *
 * @param string $url
 * @return string
 */
    public function githubUrl($url = null)
    {
        return $this->_githubUrl($url);
    }

    public function out($message = null, $newlines = 1, $level = Shell::NORMAL)
    {
    }

    public function err($message = null, $newlines = 1)
    {
    }
}

/**
 * Install Shell Test
 *
 * @category Test
 * @package  Croogo
 * @version  1.4
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class InstallShellTest extends CroogoTestCase
{

/**
 * fixtures
 *
 * @var array
 */
    public $fixtures = [
//		'plugin.croogo\settings.setting',
    ];

/**
 * setUp method
 *
 * @return void
 */
    public function setUp()
    {
        parent::setUp();

        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

//		App::build(array(
//			'Plugin' => array(Plugin::path('Croogo') . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS),
//			'View' => array(Plugin::path('Croogo') . 'Test' . DS . 'test_app' . DS . 'View' . DS),
//		), App::PREPEND);
    }

/**
 * tearDown
 *
 * @return void
 */
    public function tearDown()
    {
        parent::tearDown();
        $Folder = new Folder(TMP);
        $files = $Folder->find('croogo_.*');
        foreach ($files as $file) {
            unlink(TMP . $file);
        }
        $Folder = new Folder(Plugin::path('Croogo/Core') . 'tests' . DS . 'test_app' . DS . 'plugins' . DS . 'Example');
        $Folder->delete();
        $Folder = new Folder(Plugin::path('Croogo/Core') . 'tests' . DS . 'test_app' . DS . 'plugins' . DS . 'Minimal');
        $Folder->delete();
    }

/**
 * testInstallPlugin
 *
 * @return void
 */
    public function testInstallPlugin()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $Shell = $this->getMock('\\Croogo\\Croogo\\Shell\\InstallShell', ['out', 'err', '_shellExec', 'dispatchShell']);
        $Shell->expects($this->once())
            ->method('_shellExec')
            ->will($this->returnCallback([$this, 'callbackDownloadPlugin']));
        $Shell->expects($this->once())
            ->method('dispatchShell')
            ->with(
                $this->equalTo('ext'),
                $this->equalTo('activate'),
                $this->equalTo('plugin'),
                $this->equalTo('Example')
            )
            ->will($this->returnValue(true));
        $Shell->args = ['plugin', 'shama', 'croogo'];
        $Shell->main();
    }

/**
 * testInstallTheme
 *
 * @return void
 */
    public function testInstallTheme()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $Shell = $this->getMock('\\Croogo\\Croogo\\Shell\\InstallShell', ['out', 'err', '_shellExec', 'dispatchShell']);
        $Shell->expects($this->once())
            ->method('_shellExec')
            ->will($this->returnCallback([$this, 'callbackDownloadTheme']));
        $Shell->expects($this->once())
            ->method('dispatchShell')
            ->with(
                $this->equalTo('ext'),
                $this->equalTo('activate'),
                $this->equalTo('theme'),
                $this->equalTo('Minimal')
            )
            ->will($this->returnValue(true));
        $Shell->args = ['theme', 'shama', 'mytheme'];
        $Shell->main();
    }

/**
 * testGithubUrl
 */
    public function testGithubUrl()
    {
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
    public function testComposerInstall()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->skipIf(version_compare(PHP_VERSION, '5.3.0', '<'), 'PHP >= 5.3.0 required to run this test.');

        $Shell = new ReflectionClass('\\Croogo\\Croogo\\Shell\\InstallShell');
        $prop = $Shell->getProperty('_ExtensionsInstaller');
        $prop->setAccessible(true);
        $ShellMock = $this->getMock('\\Croogo\\Croogo\\Shell\\InstallShell', ['dispatchShell', 'out', 'err']);

        $ExtensionsInstaller = $this->getMock('ExtensionsInstaller', ['composerInstall']);
        $prop->setValue($ShellMock, $ExtensionsInstaller);

        $ExtensionsInstaller->expects($this->once())
            ->method('composerInstall')
            ->with(
                $this->equalTo([
                    'package' => 'shama/ftp',
                    'version' => '1.1.1',
                    'type' => 'plugin',
                ])
            )
            ->will($this->returnValue(['returnValue' => 0]));

        $prop = $Shell->getProperty('_CroogoPlugin');
        $prop->setAccessible(true);
        $CroogoPlugin = $this->getMock('\\Croogo\Extensions\\CroogoPlugin');
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

        $ShellMock->args = ['plugin', 'shama/ftp', '1.1.1'];
        $ShellMock->main();
    }

/**
 * Called when we want to pretend to download a plugin
 */
    public function callbackDownloadPlugin()
    {
        $argOne = func_get_arg(0);
        preg_match('/ -o (.+) /', $argOne, $zip);
        $dest = $zip[1];
        $src = Plugin::path('Croogo/Extensions') . 'tests' . DS . 'test_files' . DS . 'example_plugin.zip';
        copy($src, $dest);
        return 'Here is that thing you wanted';
    }

/**
 * Called when we want to pretend to download a theme
 */
    public function callbackDownloadTheme()
    {
        $argOne = func_get_arg(0);
        preg_match('/ -o (.+) /', $argOne, $zip);
        $dest = $zip[1];
        $src = Plugin::path('Croogo/Extensions') . 'tests' . DS . 'test_files' . DS . 'example_theme.zip';
        copy($src, $dest);
        return 'Here is that thing you wanted';
    }
}
