<?php

namespace Croogo\Blocks\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Croogo\Core\Status;
use Croogo\Core\TestSuite\IntegrationTestCase;

/**
 * @property \Croogo\Blocks\Model\Table\BlocksTable Blocks
 */
class BlocksControllerTest extends IntegrationTestCase
{
    public $fixtures = [
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
        'plugin.croogo/users.role',
        'plugin.croogo/core.settings',
        'plugin.croogo/taxonomy.taxonomy',
        'plugin.croogo/taxonomy.term',
        'plugin.croogo/taxonomy.type',
        'plugin.croogo/taxonomy.types_vocabulary',
        'plugin.croogo/users.user',
        'plugin.croogo/taxonomy.vocabulary',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->user('admin');

        $this->Blocks = TableRegistry::get('Croogo/Blocks.Blocks');
    }

    public function testAdminIndex()
    {
        $this->get('/admin/blocks/blocks/index');

        $this->assertNotEmpty($this->viewVariable('blocks'));
    }

    public function testAdminIndexSearch()
    {
        $this->get('/admin/blocks/blocks/index?title=Recent');

        $this->assertNotEmpty($this->viewVariable('blocks'));
        $this->assertEquals(1, $this->viewVariable('blocks')->count());
        $this->assertEquals(9, $this->viewVariable('blocks')->first()->id);
    }

    public function testAdminAdd()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/admin/blocks/blocks/add', [
            'title' => 'Test block',
            'alias' => 'test_block',
            'class' => 'test-block',
            'show_title' => 'test_block',
            'region_id' => 4, // right
            'body' => 'text here',
            'visibility_paths' => '',
            'status' => 1,
        ]);

        $this->assertRedirect();
        $this->assertFlash('Successfully created block');

        $block = $this->Blocks
            ->findByAlias('test_block')
            ->first();
        $this->assertEquals('Test block', $block->title);
    }

    public function testAdminEdit()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/admin/blocks/blocks/edit/3', [
            'id' => 3, // About
            'title' => 'About [modified]',
            'visibility_paths' => '',
        ]);

        $this->assertRedirect();
        $this->assertFlash('Successfully updated block');

        $block = $this->Blocks
            ->findByAlias('about')
            ->first();
        $this->assertEquals('About [modified]', $block->title);
    }

    public function testAdminDelete()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/admin/blocks/blocks/delete/8');

        $this->assertRedirect();
        $this->assertFlash('Successfully deleted block');

        $block = (bool)$this->Blocks
            ->findByAlias('search')
            ->count();
        $this->assertFalse($block);
    }

    public function testAdminMoveUp()
    {
        $this->post('/admin/blocks/blocks/moveUp/3');

        $this->assertRedirect();
        $this->assertFlash('Successfully moved block up');

        $list = $this->Blocks->find('list')->toArray();
        $this->assertEquals([
            3 => 'About',
            5 => 'Meta',
            6 => 'Blogroll',
            7 => 'Categories',
            8 => 'Search',
            9 => 'Recent Posts',
            10 => 'Block Visible by Public',
            11 => 'Block Visible by Admin or Registered'
        ], $list);
    }

    public function testAdminMoveUpWithSteps()
    {
        $this->post('/admin/blocks/blocks/moveUp/6/3');

        $this->assertRedirect();
        $this->assertFlash('Successfully moved block up');

        $list = $this->Blocks->find('list')->toArray();
        $this->assertEquals([
            6 => 'Blogroll',
            3 => 'About',
            5 => 'Meta',
            7 => 'Categories',
            8 => 'Search',
            9 => 'Recent Posts',
            10 => 'Block Visible by Public',
            11 => 'Block Visible by Admin or Registered'
        ], $list);
    }

    public function testAdminMoveDown()
    {
        $this->post('/admin/blocks/blocks/moveDown/3');

        $this->assertRedirect();
        $this->assertFlash('Successfully moved block down');

        $list = $this->Blocks->find('list')->toArray();
        $this->assertEquals([
            6 => 'Blogroll',
            5 => 'Meta',
            3 => 'About',
            7 => 'Categories',
            8 => 'Search',
            9 => 'Recent Posts',
            10 => 'Block Visible by Public',
            11 => 'Block Visible by Admin or Registered'
        ], $list);
    }

    public function testAdminMoveDownWithSteps()
    {
        $this->post('/admin/blocks/blocks/moveDown/8/3');

        $this->assertRedirect();
        $this->assertFlash('Successfully moved block down');

        $list = $this->Blocks->find('list')->toArray();
        $this->assertEquals([
            6 => 'Blogroll',
            5 => 'Meta',
            3 => 'About',
            7 => 'Categories',
            8 => 'Search',
            9 => 'Recent Posts',
            10 => 'Block Visible by Public',
            11 => 'Block Visible by Admin or Registered'
        ], $list);
    }

    public function testAdminProcessDelete()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/admin/blocks/blocks/process', [
            'Blocks' => [
                'action' => 'delete',
                '8' => ['id' => 0], // Search
                '3' => ['id' => 1], // About
                '7' => ['id' => 0], // Categories
                '6' => ['id' => 1], // Blogroll
                '9' => ['id' => 0], // Recent Posts
                '5' => ['id' => 1], // Meta
            ]
        ]);

        $this->assertRedirect();
        $this->assertFlash('Successfully deleted blocks');

        $list = $this->Blocks->find('list')->toArray();
        $this->assertEquals([
            7 => 'Categories',
            8 => 'Search',
            9 => 'Recent Posts',
            10 => 'Block Visible by Public',
            11 => 'Block Visible by Admin or Registered'
        ], $list);
    }

    public function testAdminProcessPublish()
    {
        $about = $this->Blocks->get(3);
        $about->status = Status::UNPUBLISHED;
        $this->Blocks->save($about);

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/admin/blocks/blocks/process', [
            'Blocks' => [
                'action' => 'publish',
                '8' => ['id' => 1], // Search
                '3' => ['id' => 1], // About
                '7' => ['id' => 1], // Categories
                '6' => ['id' => 1], // Blogroll
                '9' => ['id' => 1], // Recent Posts
                '5' => ['id' => 1], // Meta
            ],
        ]);

        $this->assertRedirect();
        $this->assertFlash('Successfully published blocks');

        $list = $this->Blocks
            ->find('list')
            ->where([
                'status' => true
            ])
            ->toArray();
        $this->assertEquals([
            7 => 'Categories',
            8 => 'Search',
            9 => 'Recent Posts',
            10 => 'Block Visible by Public',
            11 => 'Block Visible by Admin or Registered',
            3 => 'About',
            6 => 'Blogroll',
            5 => 'Meta'
        ], $list);
    }

    public function testAdminProcessUnpublish()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/admin/blocks/blocks/process', [
            'Blocks' => [
                'action' => 'unpublish',
                '8' => ['id' => 1], // Search
                '3' => ['id' => 1], // About
                '7' => ['id' => 0], // Categories
                '6' => ['id' => 1], // Blogroll
                '9' => ['id' => 0], // Recent Posts
                '5' => ['id' => 1], // Meta
            ],
        ]);

        $this->assertRedirect();
        $this->assertFlash('Successfully unpublished blocks');

        $list = $this->Blocks
            ->find('list')
            ->where([
                'status' => true
            ])
            ->toArray();
        $this->assertEquals([
            7 => 'Categories',
            9 => 'Recent Posts',
            10 => 'Block Visible by Public',
            11 => 'Block Visible by Admin or Registered',
        ], $list);
    }
}
