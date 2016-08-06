<?php
namespace Croogo\Nodes\Test\TestCase\Controller\Admin;

use Cake\ORM\TableRegistry;
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

        $this->user('admin');

        $this->Nodes = TableRegistry::get('Croogo/Nodes.Nodes');
    }

    public function testAdminIndex()
    {
        $this->get('/admin/nodes/index');

        $this->assertNotEmpty($this->viewVariable('nodes')->toArray());
        $this->assertEquals(3, $this->viewVariable('nodes')->count());
        $this->assertEntityHasProperty('user', $this->viewVariable('nodes')->first());
        $this->assertEntityHasProperty('custom_fields', $this->viewVariable('nodes')->first());
    }

    public function testAdminIndexSearch()
    {
        $this->get('/admin/nodes/index?filter=about');

        $this->assertEquals(1, $this->viewVariable('nodes')->count());
        $this->assertEquals(2, $this->viewVariable('nodes')->first()->id);
        $this->assertEntityHasProperty('custom_fields', $this->viewVariable('nodes')->first());
    }

    public function testAdminLinks()
    {
        $this->get('/admin/nodes/index?links=1&filter=about');
        $this->assertLayout('admin_popup');
        $this->assertNotEmpty($this->viewVariable('nodes')->toArray());

        $about = $this->viewVariable('nodes')->first();
        $this->assertEquals('about', $about->slug);
        $this->assertEntityHasProperty('user', $about);
        $this->assertEntityHasProperty('custom_fields', $about);
    }

    public function testAdminAdd()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/admin/nodes/add', [
            'title' => 'New Node',
            'slug' => 'new-node',
            'body' => '',
        ]);

        $this->assertRedirect();
        $this->assertFlash('Successfully created node');

        $newBlog = $this->Nodes
            ->find('bySlug', [
                'slug' => 'new-node',
                'type' => 'node'
            ])
            ->first();
        $this->assertEquals('New Node', $newBlog->title);
        $this->assertEquals('node', $newBlog->type);
        $this->assertNotEmpty($newBlog->created);
        $this->assertNotEquals('0', $newBlog->created->toUnixString());
    }

    public function testAdminAddBlog()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/admin/nodes/add/blog', [
            'title' => 'New Blog',
            'slug' => 'new-blog',
            'body' => '',
        ]);

        $this->assertRedirect();
        $this->assertFlash('Successfully created blog');

        $newBlog = $this->Nodes
            ->find('bySlug', [
                'slug' => 'new-blog',
                'type' => 'blog'
            ])
            ->first();
        $this->assertEquals('New Blog', $newBlog->title);
        $this->assertEquals('blog', $newBlog->type);
        $this->assertNotEmpty($newBlog->created);
        $this->assertNotEquals('0', $newBlog->created->toUnixString());
    }

    public function testAdminAddCustomCreated()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $title = 'New Blog (custom created value)';
        $slug = 'new-blog-custom-created-value';

        $this->post('/admin/nodes/add', [
            'title' => $title,
            'slug' => $slug,
            'type' => 'blog',
            'body' => '',
            'created' => '2012-03-24 01:02:03'
        ]);

        $this->assertRedirect();
        $this->assertFlash('Successfully created blog');

        $newBlog = $this->Nodes
            ->find('bySlug', [
                'slug' => $slug,
                'type' => 'blog'
            ])
            ->first();
        $this->assertEquals($title, $newBlog->title);
        $this->assertNotEmpty($newBlog->created);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAdminProcessWithInvalidAction()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/admin/nodes/process', [
            'Nodes' => [
                'action' => 'avadakadavra',
                '1' => ['id' => 0],
                '2' => ['id' => 1],
            ],
        ]);
    }

    public function testAdminProcessDataFormat()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/admin/nodes/process', [
            'Nodes' => [
                'checkAll' => '0',
                'action' => 'unpublish',
                '1' => ['id' => 0],
                '2' => ['id' => 1],
            ],
        ]);

        $this->assertEquals(1, $this->Nodes->get(1)->status);
        $this->assertEquals(0, $this->Nodes->get(2)->status);
    }

    public function testAdminEdit()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/admin/nodes/edit/1', [
            'id' => 1,
            'title' => 'Hello World [modified]',
            'slug' => 'hello-world',
            'type' => 'blog',
        ]);

        $this->assertRedirect();
        $this->assertFlash('Successfully updated blog');

        $node = $this->Nodes
            ->find('bySlug', [
                'slug' => 'hello-world',
                'type' => 'blog'
            ])
            ->first();
        $this->assertEquals('Hello World [modified]', $node->title);
    }

    public function testAdminDelete()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/admin/nodes/delete/1');

        $this->assertRedirect();
        $this->assertFlash('Successfully deleted node');

        $node = (bool)$this->Nodes
            ->find('bySlug', [
                'slug' => 'hello-world',
                'type' => 'blog'
            ])
            ->count();
        $this->assertFalse($node);
    }
//
///**
// * testBlackholedRequest
// *
// * @return void
// */
//    public function testBlackholedRequest()
//    {
//        $request = new Request('/admin/nodes/nodes/delete/1');
//        $response = new Response();
//        $this->Nodes = new TestNodesController($request, $response);
//        $this->Nodes->constructClasses();
//        $this->Nodes->request->params['plugin'] = 'nodes';
//        $this->Nodes->request->params['controller'] = 'nodes';
//        $this->Nodes->request->params['action'] = 'admin_delete';
//        $this->Nodes->request->params['prefix'] = 'admin';
//        $this->Nodes->request->params['pass'] = [];
//        $this->Nodes->request->params['named'] = [];
//        $this->Nodes->startupProcess();
//        $this->Nodes->Node->Behaviors->detach('Tree');
//        $this->Nodes->invokeAction($request);
//        $this->assertTrue($this->Nodes->blackholed);
//        $hasAny = $this->Nodes->Node->hasAny([
//            'Node.id' => 1,
//        ]);
//        $this->assertTrue($hasAny);
//    }
//
}
