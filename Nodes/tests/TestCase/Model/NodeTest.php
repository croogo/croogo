<?php
namespace Croogo\Nodes\Test\TestCase\Model;

use Croogo\TestSuite\CroogoTestCase;
use Nodes\Model\Node;

class NodeTest extends CroogoTestCase
{

    public $testBody = 'body set from event';

    public $fixtures = [
        'plugin.users.aco',
        'plugin.users.aro',
        'plugin.users.aros_aco',
        'plugin.blocks.block',
        'plugin.comments.comment',
        'plugin.contacts.contact',
        'plugin.translate.i18n',
        'plugin.settings.language',
        'plugin.menus.link',
        'plugin.menus.menu',
        'plugin.contacts.message',
        'plugin.meta.meta',
        'plugin.nodes.node',
        'plugin.taxonomy.model_taxonomy',
        'plugin.blocks.region',
        'plugin.users.role',
        'plugin.settings.setting',
        'plugin.taxonomy.taxonomy',
        'plugin.taxonomy.term',
        'plugin.taxonomy.type',
        'plugin.taxonomy.types_vocabulary',
        'plugin.users.user',
        'plugin.taxonomy.vocabulary',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Node = ClassRegistry::init('Nodes.Node');
        $this->Node->Behaviors->unload('Acl');
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->Node);
    }

/**
 * Test before Callbacks.
 */
    public function testBeforeSave()
    {
        $this->Node->type = 'whut ?';
        $data = [
            'user_id' => 42,
            'title' => 'Test Content',
            'slug' => 'test-content',
            'token_key' => 1,
            'body' => '',
            'path' => '/no-way'
        ];
        $result = $this->Node->save($data);
        $this->assertTrue((bool)$result);
        $this->assertEquals('whut ?', $result['Node']['type']);
    }

    public function testBeforeFind()
    {
        $this->Node->type = 'blog';
        $node = $this->Node->find('first', ['conditions' => ['DATE(created)' => '2009-12-25'], 'recursive' => -1]);
        $this->assertNotEmpty($node);

        $expectedNodeId = 1;

        $this->assertEquals($expectedNodeId, $node['Node']['id']);
        $this->assertEquals('blog', $node['Node']['type']);
    }

    public function testCacheTerms()
    {
        $this->Node->data = [
            'Node' => [],
            'Taxonomy' => [
                'Taxonomy' => [1, 2], // uncategorized, and announcements
            ],
        ];
        $this->Node->cacheTerms();

        $terms = json_decode($this->Node->data['Node']['terms'], true);
        ksort($terms, SORT_NUMERIC);
        $result = json_encode($terms);

        $expected = '{"1":"uncategorized","2":"announcements"}';
        $this->assertEquals($expected, $result);
    }

    public function testNodeDeleteDependent()
    {
        // assert existing count
        $commentCount = $this->Node->Comment->find('count', [
            'conditions' => [
                'Comment.model' => 'Node',
                'Comment.foreign_key' => 1
            ]
        ]);
        $this->assertEquals(2, $commentCount);

        $metaCount = $this->Node->Meta->find(
            'count',
            ['conditions' => ['model' => 'Node', 'foreign_key' => 1]]
        );
        $this->assertEquals(1, $metaCount);

        // delete node
        $this->Node->id = 1;
        $this->Node->delete();

        $commentCount = $this->Node->Comment->find('count', [
            'conditions' => [
                'Comment.model' => 'Node',
                'Comment.foreign_key' => 1,
            ]
        ]);
        $this->assertEqual(0, $commentCount);

        $metaCount = $this->Node->Meta->find(
            'count',
            ['conditions' => ['model' => 'Node', 'foreign_key' => 1]]
        );
        $this->assertEqual(0, $metaCount);
    }

/**
 * test saving node.
 */
    public function testAddNode()
    {
        $this->Node->Behaviors->disable('Tree');
        $this->Node->type = null;
        $oldNodeCount = $this->Node->find('count');

        $data = [
            'Node' => [
                'title' => 'Test Content',
                'slug' => 'test-content',
                'type' => 'blog',
                'token_key' => 1,
                'body' => '',
            ],
            'TaxonomyData' => [
                1 => [1],
            ]
        ];
        $result = $this->Node->saveNode($data, Node::DEFAULT_TYPE);
        $this->Node->type = null;
        $newNodeCount = $this->Node->find('count');

        $this->assertTrue($result);
        $this->assertTrue($this->Node->Behaviors->enabled('Tree'));
        $this->assertEquals($oldNodeCount + 1, $newNodeCount);
    }

/**
 * test saving node with non default type.
 */
    public function testAddNodeNonDefaultType()
    {
        CroogoRouter::connect('/blog/:slug', [
            'plugin' => 'nodes',
            'controller' => 'nodes',
            'action' => 'view',
            'type' => 'blog',
        ]);
        $this->Node->Behaviors->disable('Tree');
        $this->Node->type = null;
        $oldNodeCount = $this->Node->find('count');

        $data = [
            'Node' => [
                'title' => 'Test Content',
                'slug' => 'test-content',
                'token_key' => 1,
                'body' => '',
            ],
            'TaxonomyData' => [
                1 => [1],
            ]
        ];
        $result = $this->Node->saveNode($data, 'blog');
        $this->Node->type = null;
        $newNode = $this->Node->findById($this->Node->id);
        $newNodeCount = $this->Node->find('count');

        $this->assertTrue($result);
        $this->assertTrue($this->Node->Behaviors->enabled('Tree'));
        $this->assertEquals($oldNodeCount + 1, $newNodeCount);
        $this->assertEquals('/blog/test-content', $newNode['Node']['path']);
    }

/**
 * testAddNodeWithTaxonomyData
 */
    public function testAddNodeWithTaxonomyData()
    {
        $this->Node->type = null;
        $oldNodeCount = $this->Node->find('count');

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
        $result = $this->Node->saveNode($data, Node::DEFAULT_TYPE);
        $this->Node->type = null;
        $newNodeCount = $this->Node->find('count');

        $this->assertTrue($result);
        $this->assertEquals($oldNodeCount + 1, $newNodeCount);
    }

/**
 * testAddNodeWithTaxonomyMultipleTerms
 */
    public function testAddNodeWithTaxonomyMultipleTerms()
    {
        $this->Node->type = null;
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
        $result = $this->Node->saveNode($data, Node::DEFAULT_TYPE);
        $this->assertTrue($result);
        $this->assertEmpty($this->Node->validationErrors);
        $this->Node->type = null;
    }

/**
 * testAddNodeWithTaxonomyRequiredValidationError
 */
    public function testAddNodeWithTaxonomyRequiredValidationError()
    {
        $this->Node->type = null;
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
        $result = $this->Node->saveNode($data, Node::DEFAULT_TYPE);
        $this->assertFalse($result);
        $this->assertEquals('Please select at least 1 value', $this->Node->validationErrors['TaxonomyData.1'][0]);
        $this->Node->type = null;
    }

/**
 * testAddNodeWithTaxonomyNonMultipleValidationError
 */
    public function testAddNodeWithTaxonomyNonMultipleValidationError()
    {
        $this->Node->Taxonomy->Vocabulary->id = 1;
        $this->Node->Taxonomy->Vocabulary->saveField('multiple', false);
        $this->Node->type = null;
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
        $result = $this->Node->saveNode($data, Node::DEFAULT_TYPE);
        $this->assertFalse($result);
        $this->assertEquals('Please select at most 1 value', $this->Node->validationErrors['TaxonomyData.1'][0]);
        $this->Node->type = null;
        $this->Node->Taxonomy->Vocabulary->id = 1;
        $this->Node->Taxonomy->Vocabulary->saveField('multiple', true);
    }

/**
 * testAddNodeWithVisibilityRole
 */
    public function testAddNodeWithVisibilityRole()
    {
        $this->Node->type = null;
        $oldNodeCount = $this->Node->find('count');

        $data = [
            'Node' => [
                'title' => 'Test Content',
                'slug' => 'test-content',
                'type' => 'blog',
                'token_key' => 1,
                'body' => '',
            ],
            'Role' => ['Role' => ['3']], //Public
            'TaxonomyData' => [
                1 => [1],
            ]
        ];
        $result = $this->Node->saveNode($data, Node::DEFAULT_TYPE);
        $this->Node->type = null;
        $newNodeCount = $this->Node->find('count');

        $this->assertTrue($result);
        $this->assertEquals($oldNodeCount + 1, $newNodeCount);
    }

/**
 * Test onBeforeSaveNode Event Callbacks
 */
    public function onBeforeSaveNode($Event)
    {
        $Event->data['data']['Node']['body'] = $this->testBody;
    }

/**
 * Test onAfterSaveNode Event Callbacks
 */
    public function onAfterSaveNode($Event)
    {
        $this->assertEquals($this->testBody, $Event->data['data']['Node']['body']);
    }

/**
 * testSaveNodeEvents
 */
    public function testSaveNodeEvents()
    {
        $this->Node->type = null;
        $oldNodeCount = $this->Node->find('count');

        $data = [
            'Node' => [
                'title' => 'Test Content',
                'slug' => 'test-content',
                'type' => 'blog',
                'token_key' => 1,
                'body' => '',
            ],
            'Role' => ['Role' => ['3']], //Public
            'TaxonomyData' => [
                1 => [1],
            ]
        ];

        $manager = EventManager::instance();
        $manager->attach([$this, 'onBeforeSaveNode'], 'Model.Node.beforeSaveNode');
        $manager->attach([$this, 'onAfterSaveNode'], 'Model.Node.afterSaveNode');

        $result = $this->Node->saveNode($data, Node::DEFAULT_TYPE);
        $this->Node->type = null;
        $node = $this->Node->find('first', [
            'fields' => ['id', 'title', 'slug', 'body'],
            'recursive' => -1,
            'conditions' => [
                'Node.id' => $this->Node->id,
            ],
        ]);

        $this->assertTrue($result);
        $this->assertEquals('Test Content', $node['Node']['title']);
        $this->assertEquals($this->testBody, $node['Node']['body']);

        $manager->detach([$this, 'onBeforeSaveNode']);
        $manager->detach([$this, 'onAfterSaveNode']);
    }

/**
 * testAddNodeWithInvalidNodeType
 */
    public function testAddNodeWithInvalidNodeType()
    {
        $this->setExpectedException('InvalidArgumentException');
        $data = [
            'title' => 'Test Content',
            'slug' => 'test-content',
            'type' => 'invalid',
            'token_key' => 1,
            'body' => '',
        ];
        $result = $this->Node->saveNode($data, 'invalid');
    }

/**
 * Test filtering methods
 */
    public function testFilterNodesByTitle()
    {
        $filterConditions = $this->Node->filterNodes(['filter' => 'Hello']);
        $node = $this->Node->find('first', ['conditions' => $filterConditions]);

        $this->assertNotEmpty($node);
        $this->assertEquals(1, $node['Node']['id']);
    }

    public function testFilterNodesByBody()
    {
        $filterConditions = $this->Node->filterNodes(['filter' => 'example']);
        $node = $this->Node->find('first', ['conditions' => $filterConditions]);

        $this->assertNotEmpty($node);
        $this->assertEquals(2, $node['Node']['id']);
    }

    public function testFilterNodesWithoutKeyword()
    {
        $filterConditions = $this->Node->filterNodes();
        $nodes = $this->Node->find('all', ['conditions' => $filterConditions]);

        $this->assertEquals(3, count($nodes));
    }

/**
 * test updateAllNodesPaths
 */
    public function testUpdateAllNodesPaths()
    {

        $this->Node->id = 1;
        $result = $this->Node->saveField('path', 'invalid one');
        $this->assertTrue((bool)$result);

        CroogoRouter::connect('/blog/:slug', [
            'plugin' => 'nodes',
            'controller' => 'nodes',
            'action' => 'view',
            'type' => 'blog',
        ]);
        Router::promote();
        $result = $this->Node->updateAllNodesPaths();
        $this->assertTrue($result);
        $this->Node->type = 'blog';
        $node = $this->Node->findById(1);
        $this->assertEquals('/blog/hello-world', $node['Node']['path']);
    }

/**
 * Test find('promoted')
 */
    public function testFindPromoted()
    {
        $results = $this->Node->find('promoted');
        $expectedId = 1;

        $this->assertEquals(1, count($results));
        $this->assertEquals($expectedId, $results[0]['Node']['id']);
        $this->assertEquals(Node::STATUS_PUBLISHED, $results[0]['Node']['status']);
        $this->assertEquals(Node::STATUS_PROMOTED, $results[0]['Node']['promote']);
    }

/**
 * test processActionDelete
 */
    public function testProcessActionDelete()
    {
        $ids = ['1', '2'];

        $commentCount = $this->Node->Comment->find('count', [
            'conditions' => [
                'Comment.model' => 'Node',
                'Comment.foreign_key' => $ids,
            ]
        ]);
        $this->assertTrue($commentCount > 0);

        $success = $this->Node->processAction('delete', $ids);
        $count = $this->Node->find('count');

        $this->assertTrue($success);
        $this->assertEquals(1, $count);

        // verifies that related comments are deleted (by afterDelete callback)
        $commentCount = $this->Node->Comment->find('count', [
            'conditions' => [
                'Comment.model' => 'Node',
                'Comment.foreign_key' => $ids,
            ]
        ]);
        $this->assertTrue($commentCount === 0);
    }

/**
 * test processActionPromote
 */
    public function testProcessActionPromote()
    {
        $ids = ['1', '2'];

        $success = $this->Node->processAction('promote', $ids);
        $newRecords = $this->Node->find('all');

        $this->assertTrue($success);
        foreach ($newRecords as $record) {
            $this->assertTrue($record['Node']['promote']);
        }
    }

/**
 * test processActionUnpromote
 */
    public function testProcessActionUnpromote()
    {
        $ids = ['1', '2', '3'];

        $success = $this->Node->processAction('unpromote', $ids);
        $newRecords = $this->Node->find('all');

        $this->assertTrue($success);
        foreach ($newRecords as $record) {
            $this->assertFalse($record['Node']['promote']);
        }
    }

/**
 * test processActionPublish
 */
    public function testProcessActionPublish()
    {
        $ids = ['1', '2'];

        $success = $this->Node->processAction('publish', $ids);
        $newRecords = $this->Node->find('all');

        $this->assertTrue($success);
        foreach ($newRecords as $record) {
            $this->assertEquals(CroogoStatus::PUBLISHED, $record['Node']['status']);
        }
    }

/**
 * test processActionUnpublish
 */
    public function testProcessActionUnpublish()
    {
        $ids = ['1', '2', '3'];

        $success = $this->Node->processAction('unpublish', $ids);
        $newRecords = $this->Node->find('all');

        $this->assertTrue($success);
        foreach ($newRecords as $record) {
            $this->assertEquals(CroogoStatus::UNPUBLISHED, $record['Node']['status']);
        }
    }

/**
 * test processActionInvalidAction
 */
    public function testProcessActionInvalidAction()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->Node->processAction('avadakadavra', [1, 2]);
    }

/**
 * test processActionWithoutIds
 */
    public function testProcessActionWithoutIds()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->Node->processAction('delete', []);
    }

/**
 * testFindViewById
 */
    public function testFindViewById()
    {
        $this->Node->useCache = false;
        $node = $this->Node->find('viewById', [
            'id' => 1,
        ]);
        $this->assertEquals('Hello World', $node['Node']['title']);
    }

/**
 * testFindViewBySlug
 */
    public function testFindViewBySlug()
    {
        $this->Node->useCache = false;
        $node = $this->Node->find('viewBySlug', [
            'slug' => 'about',
            'type' => 'page',
        ]);
        $this->assertEquals('About', $node['Node']['title']);
    }

/**
 * testFindPublish
 */
    public function testFindPublished()
    {
        $Node = $this->Node;
        $Node->useCache = false;
        $Node->id = 3;
        $Node->saveField('status', 0);

        $nodes = $Node->find('published', [
            'fields' => ['id'],
        ]);
        $extracted = Hash::extract($nodes, '{n}.Node.status');
        $this->assertEquals(2, count($nodes));
    }

/**
 * testFormatDataPreserveSuppliedPath
 */
    public function testFormatDataPreserveSuppliedPath()
    {
        $Node = $this->Node;
        $result = $Node->formatData([
            'Node' => [
                'slug' => 'foo',
                'path' => '/bar/foo',
            ],
        ]);
        $this->assertEquals('/bar/foo', $result['Node']['path']);
    }
}
