<?php

namespace Croogo\Nodes\Test\TestCase\View\Helper;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\View\View;
use Croogo\Core\Croogo;
use Croogo\Core\Event\EventManager;
use Croogo\Core\Plugin;
use Croogo\Core\TestSuite\TestCase;
use Croogo\Nodes\View\Helper\NodesHelper;

/**
 * @property \Croogo\Nodes\View\Helper\NodesHelper helper
 * @property \Cake\View\View view
 * @property \Croogo\Nodes\Model\Table\NodesTable Nodes
 */
class NodesHelperTest extends TestCase
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

        Plugin::routes();
        Plugin::events();
        EventManager::loadListeners();

        $this->view = new View;
        $this->helper = new NodesHelper($this->view);
        $this->Nodes = TableRegistry::get('Croogo/Nodes.Nodes');
    }

    /**
     * Test [node] shortcode
     */
    public function testNodeShortcode()
    {
        $content = '[node:recent_posts conditions="Nodes.type:blog" order="Nodes.id DESC" limit="5"]';
        $this->view->viewVars['nodesForLayout']['recent_posts'] = [
            $this->Nodes->get(1),
        ];
        Croogo::dispatchEvent('Helper.Layout.beforeFilter', $this->view, ['content' => &$content]);
        $this->assertContains('node-list-recent_posts', $content);
        $this->assertContains('class="node-list"', $content);
    }

    public function testNodesUrl()
    {
        $node = $this->Nodes->get(1);
        $expected = '/blog/hello-world';
        $this->assertEquals($expected, $this->helper->url($node));

        $fullBaseUrl = Configure::read('App.fullBaseUrl');
        $result = $this->helper->url($node, true);
        $this->assertEquals($fullBaseUrl . $expected, $result);
    }
}
