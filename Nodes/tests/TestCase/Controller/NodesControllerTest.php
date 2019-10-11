<?php
namespace Croogo\Nodes\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Croogo\Core\Event\EventManager;
use Croogo\Core\PluginManager;
use Croogo\Core\TestSuite\IntegrationTestCase;

/**
 * @property \Croogo\Nodes\Model\Table\NodesTable Nodes
 */
class NodesControllerTest extends IntegrationTestCase
{
    public $fixtures = [
        'plugin.Croogo/Users.Role',
        'plugin.Croogo/Users.User',
        'plugin.Croogo/Users.Aco',
        'plugin.Croogo/Users.Aro',
        'plugin.Croogo/Users.ArosAco',
        'plugin.Croogo/Blocks.Block',
        'plugin.Croogo/Comments.Comment',
        'plugin.Croogo/Contacts.Contact',
        'plugin.Croogo/Translate.I18n',
        'plugin.Croogo/Settings.Language',
        'plugin.Croogo/Menus.Link',
        'plugin.Croogo/Menus.Menu',
        'plugin.Croogo/Contacts.Message',
        'plugin.Croogo/Meta.Meta',
        'plugin.Croogo/Nodes.Node',
        'plugin.Croogo/Taxonomy.ModelTaxonomy',
        'plugin.Croogo/Blocks.Region',
        'plugin.Croogo/Core.Settings',
        'plugin.Croogo/Taxonomy.Taxonomy',
        'plugin.Croogo/Taxonomy.Term',
        'plugin.Croogo/Taxonomy.Type',
        'plugin.Croogo/Taxonomy.TypesVocabulary',
        'plugin.Croogo/Taxonomy.Vocabulary',
    ];

    public function setUp()
    {
        parent::setUp();

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
        PluginManager::load('Mytheme');
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
