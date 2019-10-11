<?php

namespace Croogo\Meta\Test\TestCase\Model\Behavior;

use Croogo\TestSuite\CroogoTestCase;
use Nodes\Model\Node;

class MetaBehaviorTest extends CroogoTestCase
{

    public $fixtures = [
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
        'plugin.Croogo/Nodes.Node',
        'plugin.Croogo/Meta.Meta',
        'plugin.Croogo/Taxonomy.ModelTaxonomy',
        'plugin.Croogo/Blocks.Region',
        'plugin.Croogo/Users.Role',
        'plugin.Croogo/Settings.Setting',
        'plugin.Croogo/Taxonomy.Taxonomy',
        'plugin.Croogo/Taxonomy.Term',
        'plugin.Croogo/Taxonomy.Type',
        'plugin.Croogo/Taxonomy.TypesVocabulary',
        'plugin.Croogo/Users.User',
        'plugin.Croogo/Taxonomy.Vocabulary',
    ];

    public $Node = null;

/**
 * setUp
 *
 * @return void
 */
    public function setUp()
    {
        parent::setUp();
        $this->Node = ClassRegistry::init('Nodes.Node');
    }

/**
 * tearDown
 *
 * @return void
 */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->Node);
        ClassRegistry::flush();
    }

    public function testSingle()
    {
        $helloWorld = $this->Node->findBySlug('hello-world');
        $this->assertEqual($helloWorld['CustomFields']['meta_keywords'], 'key1, key2');
    }

    public function testMultiple()
    {
        $result = $this->Node->find('all', [
            'order' => 'Node.id ASC',
        ]);
        $this->assertEqual($result['0']['CustomFields']['meta_keywords'], 'key1, key2');
    }

    public function testPrepareMeta()
    {
        $data = [
            'Meta' => [
                String::uuid() => [
                    'key' => 'key1',
                    'value' => 'value1',
                ],
                String::uuid() => [
                    'key' => 'key2',
                    'value' => 'value2',
                ],
            ],
        ];
        $this->assertEquals(
            [
                'Meta' => [
                    '0' => [
                        'key' => 'key1',
                        'value' => 'value1',
                    ],
                    '1' => [
                        'key' => 'key2',
                        'value' => 'value2',
                    ],
                ],
            ],
            $this->Node->prepareData($data)
        );
    }
}
