<?php

namespace Croogo\Nodes\Test\TestCase\View\Helper;

use Cake\Controller\Controller;
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
