<?php
namespace Croogo\Nodes\Test\TestCase\Model\Table;


use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Croogo\Core\Event\EventManager;
use Croogo\Core\TestSuite\TestCase;
use Croogo\Nodes\Model\Entity\Node;

class NodesTableTest extends TestCase
{

    public $testBody = 'body set from event';

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

    /**
     * @var \Croogo\Nodes\Model\Table\NodesTable
     */
    public $Nodes;

    public function setUp()
    {
        parent::setUp();

        $this->Nodes = TableRegistry::get('Croogo/Nodes.Nodes');
    }

    public function testBeforeSave()
    {
        $this->markTestIncomplete('This test needs to be ported');

        $this->Nodes->type = 'whut ?';
        $data = [
            'user_id' => 42,
            'title' => 'Test Content',
            'slug' => 'test-content',
            'token_key' => 1,
            'body' => '',
            'path' => '/no-way'
        ];
        $result = $this->Nodes->save($data);
        $this->assertTrue((bool)$result);
        $this->assertEquals('whut ?', $result->type);
    }

    public function testBeforeFind()
    {
        $node = $this->Nodes->find()->where(['DATE(created)' => '2009-12-25'])->first();
        $this->assertNotEmpty($node);

        $this->assertEquals(1, $node->id);
        $this->assertEquals('blog', $node->type);
    }

    public function testCacheTerms()
    {
        $this->markTestIncomplete('This test needs to be ported');

        $this->Nodes->data = [
            'Node' => [],
            'Taxonomy' => [
                'Taxonomy' => [1, 2], // uncategorized, and announcements
            ],
        ];
        $this->Nodes->cacheTerms();

        $terms = json_decode($this->Nodes->data['Node']['terms'], true);
        ksort($terms, SORT_NUMERIC);
        $result = json_encode($terms);

        $expected = '{"1":"uncategorized","2":"announcements"}';
        $this->assertEquals($expected, $result);
    }

    public function testNodeDeleteDependent()
    {
        // assert existing count
        $commentQuery = TableRegistry::get('Croogo/Comments.Comments')
            ->find()
            ->where([
                'model' => 'Croogo/Nodes.Nodes',
                'foreign_key' => 1
            ]);
        $metaQuery = $this->Nodes->Meta
            ->find()
            ->where([
                'model' => 'Croogo/Nodes.Nodes',
                'foreign_key' => 1
            ]);
        $this->assertQueryCount(2, $commentQuery);
        $this->assertQueryCount(1, $metaQuery);

        // delete node
        $this->Nodes->delete($this->Nodes->get(1));

        $commentQuery = TableRegistry::get('Croogo/Comments.Comments')
            ->find()
            ->where([
                'model' => 'Croogo/Nodes.Nodes',
                'foreign_key' => 1
            ]);
        $metaQuery = $this->Nodes->Meta
            ->find()
            ->where([
                'model' => 'Croogo/Nodes.Nodes',
                'foreign_key' => 1
            ]);
        $this->assertQueryCount(0, $commentQuery);
        $this->assertQueryCount(0, $metaQuery);
    }

    /**
     * test saving node.
     */
    public function testAddNode()
    {
        $oldNodeCount = $this->Nodes->find()->count();

        $node = $this->Nodes->newEntity([
            'title' => 'Test Content',
            'slug' => 'test-content',
            'type' => 'blog',
            'body' => null,
            'TaxonomyData' => [
                1 => [1],
            ]
        ]);
        $result = $this->Nodes->save($node);
        $newNodeCount = $this->Nodes->find()->count();

        $this->assertInstanceOf(Node::class, $result);
        $this->assertEquals($oldNodeCount + 1, $newNodeCount);
    }

    /**
     * test saving node with non default type.
     */
    public function testAddNodeNonDefaultType()
    {
        $oldNodeCount = $this->Nodes->find()->count();

        $node = $this->Nodes->newEntity([
            'title' => 'Test Content',
            'slug' => 'test-content',
            'type' => 'blog',
            'body' => null,
            'TaxonomyData' => [
                1 => [1],
            ]
        ]);
        $result = $this->Nodes->save($node);
        $newNodeCount = $this->Nodes->find()->count();

        $this->assertInstanceOf(Node::class, $result);
        $this->assertEquals($oldNodeCount + 1, $newNodeCount);
    }

    public function testAddNodeWithTaxonomyData()
    {
        $this->markTestIncomplete('This test needs to be ported');

        $this->Nodes->type = null;
        $oldNodeCount = $this->Nodes->find()->count();

        $data = [
            'Node' => [
                'title' => 'Test Content',
                'slug' => 'test-content',
                'type' => 'blog',
                'token_key' => 1,
                'body' => '',
            ],
            'TaxonomyData' => [1 => [0 => '1']],
        ];
        $result = $this->Nodes->saveNode($data, Node::DEFAULT_TYPE);
        $this->Nodes->type = null;
        $newNodeCount = $this->Nodes->find()->count();

        $this->assertTrue($result);
        $this->assertEquals($oldNodeCount + 1, $newNodeCount);
    }

    public function testAddNodeWithTaxonomyMultipleTerms()
    {
        $this->markTestIncomplete('This test needs to be ported');

        $this->Nodes->type = null;
        $data = [
            'Node' => [
                'title' => 'Test Content',
                'slug' => 'test-content',
                'type' => 'blog',
                'token_key' => 1,
                'body' => '',
            ],
            'TaxonomyData' => [1 => [0 => '1', 1 => 2]],
        ];
        $result = $this->Nodes->saveNode($data, Node::DEFAULT_TYPE);
        $this->assertTrue($result);
        $this->assertEmpty($this->Nodes->validationErrors);
        $this->Nodes->type = null;
    }

    public function testAddNodeWithTaxonomyRequiredValidationError()
    {
        $this->markTestIncomplete('This test needs to be ported');

        $this->Nodes->type = null;
        $data = [
            'Node' => [
                'title' => 'Test Content',
                'slug' => 'test-content',
                'type' => 'blog',
                'token_key' => 1,
                'body' => '',
            ],
            'TaxonomyData' => [1 => null],
        ];
        $result = $this->Nodes->saveNode($data, Node::DEFAULT_TYPE);
        $this->assertFalse($result);
        $this->assertEquals('Please select at least 1 value', $this->Nodes->validationErrors['TaxonomyData.1'][0]);
        $this->Nodes->type = null;
    }

    public function testAddNodeWithTaxonomyNonMultipleValidationError()
    {
        $this->markTestIncomplete('This test needs to be ported');

        $this->Nodes->Taxonomy->Vocabulary->id = 1;
        $this->Nodes->Taxonomy->Vocabulary->saveField('multiple', false);
        $this->Nodes->type = null;
        $data = [
            'Node' => [
                'title' => 'Test Content',
                'slug' => 'test-content',
                'type' => 'blog',
                'token_key' => 1,
                'body' => '',
            ],
            'TaxonomyData' => [1 => [0 => '1', 1 => 2]],
        ];
        $result = $this->Nodes->saveNode($data, Node::DEFAULT_TYPE);
        $this->assertFalse($result);
        $this->assertEquals('Please select at most 1 value', $this->Nodes->validationErrors['TaxonomyData.1'][0]);
        $this->Nodes->type = null;
        $this->Nodes->Taxonomy->Vocabulary->id = 1;
        $this->Nodes->Taxonomy->Vocabulary->saveField('multiple', true);
    }

    public function testAddNodeWithVisibilityRole()
    {
        $oldNodeCount = $this->Nodes->find()->count();

        $node = $this->Nodes->newEntity([
            'title' => 'Test Content',
            'slug' => 'test-content',
            'type' => 'blog',
            'body' => null,
            'visibility_roles' => [3],
            'TaxonomyData' => [
                1 => [1],
            ]
        ]);
        $result = $this->Nodes->save($node);
        $newNodeCount = $this->Nodes->find()->count();

        $this->assertInstanceOf(Node::class, $result);
        $this->assertEquals($oldNodeCount + 1, $newNodeCount);
    }

    /**
     * Test onBeforeSaveNode Event Callbacks
     */
    public function onBeforeSaveNode(Event $Event)
    {
        $Event->data()['node']->body = $this->testBody;
    }

    /**
     * Test onAfterSaveNode Event Callbacks
     */
    public function onAfterSaveNode(Event $event)
    {
        $this->assertEquals($this->testBody, $event->data()['node']->body);
    }

    public function testSaveNodeEvents()
    {
        $node = $this->Nodes->newEntity([
            'title' => 'Test Content',
            'slug' => 'test-content',
            'type' => 'blog',
            'body' => '',
            'visibility_roles' => [3],
            'TaxonomyData' => [
                1 => [1],
            ]
        ]);

        $manager = EventManager::instance();
        $manager->on('Model.Node.beforeSaveNode', [$this, 'onBeforeSaveNode']);
        $manager->on('Model.Node.afterSaveNode', [$this, 'onAfterSaveNode']);

        $result = $this->Nodes->save($node);

        $this->assertInstanceOf(Node::class, $result);
        $this->assertEquals('Test Content', $node->title);
        $this->assertEquals($this->testBody, $node->body);

        $manager->off('Model.Node.beforeSaveNode', [$this, 'onBeforeSaveNode']);
        $manager->off('Model.Node.afterSaveNode', [$this, 'onAfterSaveNode']);
    }

    public function testAddNodeWithInvalidNodeType()
    {
        $data = $this->Nodes->newEntity([
            'title' => 'Test Content',
            'slug' => 'test-content',
            'type' => 'invalid',
            'body' => null,
        ]);
        $this->Nodes->save($data);

        $this->assertFalse($this->Nodes->checkRules($data));
    }

    public function testFilterNodesByTitle()
    {
        $node = $this->Nodes->find('filterNodes', [
            'filter' => 'Hello'
        ])->first();

        $this->assertNotEmpty($node);
        $this->assertEquals(1, $node->id);
    }

    public function testFilterNodesByBody()
    {
        $node = $this->Nodes->find('filterNodes', [
            'filter' => 'example'
        ])->first();

        $this->assertNotEmpty($node);
        $this->assertEquals(2, $node->id);
    }

    public function testFindPromoted()
    {
        $results = $this->Nodes->find('promoted');

        $node = $results->first();
        $this->assertEquals(2, $results->count());
        $this->assertEquals(1, $node->id);
        $this->assertEquals(1, $node->status);
        $this->assertEquals(true, $node->promote);
    }

    public function testProcessActionDelete()
    {
        $ids = [1, 2];

        $commentCount = $this->Nodes->Comments->find()->where([
            'model' => 'Croogo/Nodes.Nodes',
            'foreign_key IN' => $ids,
        ])->count();
        $this->assertTrue($commentCount > 0);

        $success = $this->Nodes->processAction('delete', $ids);
        $count = $this->Nodes->find()->count();

        $this->assertTrue($success);
        $this->assertEquals(1, $count);

        // verifies that related comments are deleted (by afterDelete callback)
        $commentCount = $this->Nodes->Comments->find()->where([
            'model' => 'Croogo/Nodes.Nodes',
            'foreign_key IN' => $ids,
        ])->count();
        $this->assertEquals(0, $commentCount);
    }

    public function testProcessActionPromote()
    {
        $ids = ['1', '2'];

        $success = $this->Nodes->processAction('promote', $ids);
        $newRecords = $this->Nodes->find('all');

        $this->assertTrue($success);
        foreach ($newRecords as $record) {
            $this->assertTrue($record->promote);
        }
    }

    public function testProcessActionUnpromote()
    {
        $ids = ['1', '2', '3'];

        $success = $this->Nodes->processAction('unpromote', $ids);
        $newRecords = $this->Nodes->find('all');

        $this->assertTrue($success);
        foreach ($newRecords as $record) {
            $this->assertFalse($record->promote);
        }
    }

    public function testProcessActionPublish()
    {
        $ids = ['1', '2'];

        $success = $this->Nodes->processAction('publish', $ids);
        $newRecords = $this->Nodes->find('all');

        $this->assertTrue($success);
        foreach ($newRecords as $record) {
            $this->assertEquals(1, $record->status);
        }
    }

    public function testProcessActionUnpublish()
    {
        $ids = ['1', '2', '3'];

        $success = $this->Nodes->processAction('unpublish', $ids);
        $newRecords = $this->Nodes->find('all');

        $this->assertTrue($success);
        foreach ($newRecords as $record) {
            $this->assertEquals(0, $record->status);
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testProcessActionInvalidAction()
    {
        $this->Nodes->processAction('avadakadavra', [1, 2]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testProcessActionWithoutIds()
    {
        $this->Nodes->processAction('delete', []);
    }

    public function testFindViewById()
    {
        $node = $this->Nodes->find('viewById', [
            'id' => 1,
        ])->first();
        $this->assertEquals('Hello World', $node->title);
    }

    public function testFindViewBySlug()
    {
        $node = $this->Nodes->find('viewBySlug', [
            'slug' => 'about',
            'type' => 'page',
        ])->first();
        $this->assertEquals('About', $node->title);
    }

    public function testFindPublished()
    {
        $protected = $this->Nodes->get(3);
        $protected->status = 0;
        $this->Nodes->save($protected);

        $this->assertEquals(2, $this->Nodes->find('published')->count());
    }
}
