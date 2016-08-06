<?php
namespace Croogo\Nodes\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Croogo\Core\Event\EventManager;
use Croogo\Core\Plugin;
use Croogo\Core\TestSuite\IntegrationTestCase;

/**
 * @property \Croogo\Nodes\Model\Table\NodesTable Nodes
 */
class NodesControllerTest extends IntegrationTestCase
{
    public $fixtures = [
        'plugin.croogo/users.role',
        'plugin.croogo/users.user',
        'plugin.croogo/users.aco',
        'plugin.croogo/users.aro',
        'plugin.croogo/users.aros_aco',
        'plugin.croogo/blocks.block',
        'plugin.croogo/comments.comment',
        'plugin.croogo/contacts.contact',
        'plugin.croogo/translate.i18n',
        'plugin.croogo/settings.language',
        'plugin.croogo/menus.link',
        'plugin.croogo/menus.menu',
        'plugin.croogo/contacts.message',
        'plugin.croogo/meta.meta',
        'plugin.croogo/nodes.node',
        'plugin.croogo/taxonomy.model_taxonomy',
        'plugin.croogo/blocks.region',
        'plugin.croogo/core.settings',
        'plugin.croogo/taxonomy.taxonomy',
        'plugin.croogo/taxonomy.term',
        'plugin.croogo/taxonomy.type',
        'plugin.croogo/taxonomy.types_vocabulary',
        'plugin.croogo/taxonomy.vocabulary',
    ];

    public function setUp()
    {
        parent::setUp();

        Plugin::routes();
        Plugin::events();
        EventManager::loadListeners();

        $this->Nodes = TableRegistry::get('Croogo/Nodes.Nodes');
    }

    public function testPromotedWithVisibilityRole()
    {
        $this->user('admin');

        $this->get('/promoted');

        $this->assertEquals(2, $this->viewVariable('nodes')->count());
    }

    public function testIndexWithVisibilityRole()
    {
        $this->user('admin');

        $this->get('/node?type=page');

        $this->assertEquals(2, $this->viewVariable('nodes')->count());
    }

    public function testViewFallback()
    {
        Plugin::load('Mytheme');
        Configure::write('Site.theme', 'Mytheme');

        $this->get('/node');

        $this->_controller->Croogo->viewFallback(['index_blog']);
        $this->assertContains('index_blog', $this->_controller->viewBuilder()->template());
        $this->assertContains('Mytheme', $this->_controller->viewBuilder()->template());

        $this->get('/blog/hello-world');

        $this->_controller->Croogo->viewFallback(['view_1', 'view_blog']);
        $this->assertContains('view_1.ctp', $this->_controller->viewBuilder()->template());
        $this->assertContains('Mytheme', $this->_controller->viewBuilder()->template());
    }

    /**
     * testViewFallback for core NodesController with default theme
     *
     * @return void
     */
    public function testViewFallbackWithDefaultTheme()
    {
        $this->get('/');

        $this->_controller->Croogo->viewFallback('index_node');
        $this->assertContains('index_node.ctp', $this->_controller->viewBuilder()->template());
    }
}
