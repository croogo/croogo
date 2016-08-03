<?php

namespace Croogo\Core\Test\TestCase\Shell;

use Cake\Console\Shell;
use Cake\Console\ShellDispatcher;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Croogo\Core\TestSuite\CroogoTestCase;

/**
 * Ext Shell Test
 *
 * @category Test
 * @package  Croogo
 * @version  1.4
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExtShellTest extends CroogoTestCase
{

/**
 * fixtures
 *
 * @var array
 */
    public $fixtures = [
//		'plugin.comments.comment',
//		'plugin.menus.menu',
//		'plugin.blocks.block',
//		'plugin.menus.link',
//		'plugin.meta.meta',
//		'plugin.croogo\nodes.node',
//		'plugin.taxonomy.model_taxonomy',
//		'plugin.blocks.region',
//		'plugin.croogo\users.role',
//		'plugin.croogo/settings.setting',
//		'plugin.taxonomy.taxonomy',
//		'plugin.taxonomy.term',
//		'plugin.taxonomy.type',
//		'plugin.taxonomy.types_vocabulary',
//		'plugin.croogo\users.user',
//		'plugin.taxonomy.vocabulary',
//		'plugin.croogo\users.aro',
//		'plugin.croogo\users.aco',
//		'plugin.croogo\users.aros_aco',
    ];

/**
 * setUp method
 *
 * @return void
 */
    public function setUp()
    {
        parent::setUp();
        $Folder = new Folder(APP . 'Plugin' . DS . 'Example');
        $Folder->copy(Plugin::path('Croogo/Core') . 'tests' . DS . 'test_app' . DS . 'Plugin' . DS . 'Example');
//		$this->Setting = ClassRegistry::init('Settings.Setting');
    }

/**
 * tearDown
 *
 * @return void
 */
    public function tearDown()
    {
        parent::tearDown();
        $Folder = new Folder(Plugin::path('Croogo/Core') . 'tests' . DS . 'test_app' . DS . 'Plugin' . DS . 'Example');
        $Folder->delete();
    }

/**
 * testPlugin
 *
 * @return void
 */
    public function testPlugin()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $Link = ClassRegistry::init('Menus.Link');
        $Shell = $this->getMock('ExtShell', ['out', 'err']);

        $Shell->args = ['deactivate', 'plugin', 'Example'];
        $Shell->params = ['force' => false];
        $Shell->main();
        $result = $this->Setting->findByKey('Hook.bootstraps');
        $this->assertFalse(in_array('Example', explode(',', $result['Setting']['value'])));
        $result = $Link->findByTitle('Example');
        $this->assertFalse(!empty($result));

        $Shell->args = ['activate', 'plugin', 'Example'];
        $Shell->main();
        $result = $this->Setting->findByKey('Hook.bootstraps');
        $this->assertTrue(in_array('Example', explode(',', $result['Setting']['value'])));
        $result = $Link->findByTitle('Example');
        $this->assertTrue(!empty($result));

        $bogusPlugin = 'Bogus';
        $Shell->args = ['activate', 'plugin', $bogusPlugin];
        $Shell->main();
        $result = $this->Setting->findByKey('Hook.bootstraps');
        $this->assertFalse(in_array($bogusPlugin, explode(',', $result['Setting']['value'])));
    }

/**
 * testForceActivation
 */
    public function testForceActivation()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $Shell = $this->getMock('ExtShell', ['out', 'err']);

        $Shell->args = ['activate', 'plugin', 'TestPlugin'];
        $Shell->main();
        $result = $this->Setting->findByKey('Hook.bootstraps');
        $this->assertFalse(in_array('TestPlugin', explode(',', $result['Setting']['value'])));

        $Shell->args = ['activate', 'plugin', 'TestPlugin'];
        $Shell->params = ['force' => true];
        $Shell->main();
        $result = $this->Setting->findByKey('Hook.bootstraps');
        $this->assertTrue(in_array('TestPlugin', explode(',', $result['Setting']['value'])));
    }

/**
 * testForceDeactivation
 */
    public function testForceDeactivation()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $Shell = $this->getMock('ExtShell', ['out', 'err']);

        $result = $this->Setting->findByKey('Hook.bootstraps');
        $bogus = $result['Setting']['value'] . ',Bogus';
        $this->Setting->write('Hook.bootstraps', $bogus);

        $Shell->args = ['deactivate', 'plugin', 'Bogus'];
        $Shell->params['force'] = true;
        $Shell->main();

        $result = $this->Setting->findByKey('Hook.bootstraps');
        $this->assertFalse(in_array('Bogus', explode(',', $result['Setting']['value'])));
    }

/**
 * testTheme
 *
 * @return void
 */
    public function testTheme()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $Shell = $this->getMock('ExtShell', ['out', 'err']);
        $Shell->args = ['activate', 'theme', 'Mytheme'];
        $Shell->main();
        $result = $this->Setting->findByKey('Site.theme');
        $this->assertEquals('Mytheme', $result['Setting']['value']);
        $this->assertEquals('Mytheme', Configure::read('Site.theme'));

        $Shell->args = ['activate', 'theme', 'Bogus'];
        $Shell->main();
        $result = $this->Setting->findByKey('Site.theme');
        $this->assertEquals('Mytheme', $result['Setting']['value']);
        $this->assertEquals('Mytheme', Configure::read('Site.theme'));

        $Shell->args = ['deactivate', 'theme'];
        $Shell->main();
        $result = $this->Setting->findByKey('Site.theme');
        $this->assertEquals('Mytheme', $result['Setting']['value']);

        $Shell->args = ['deactivate', 'theme', 'Mytheme'];
        $Shell->main();
        $result = $this->Setting->findByKey('Site.theme');
        $this->assertEquals('Mytheme', $result['Setting']['value']);

        $Shell->args = ['activate', 'theme', 'Mytheme'];
        $Shell->main();
        $Shell->args = ['activate', 'theme', 'default'];
        $Shell->main();
        $result = $this->Setting->findByKey('Site.theme');
        $this->assertEquals('', $result['Setting']['value']);
    }
}
