<?php
namespace Croogo\Taxonomy\Test\TestCase\Model;

use Croogo\TestSuite\CroogoTestCase;
use Taxonomy\Model\Taxonomy;

class TaxonomyTest extends CroogoTestCase
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
        'plugin.Croogo/Meta.Meta',
        'plugin.Croogo/Nodes.Node',
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

    public function setUp()
    {
        parent::setUp();
        $this->Taxonomy = ClassRegistry::init('Taxonomy.Taxonomy');
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->Taxonomy);
    }

    public function testGetTree()
    {
        $tree = $this->Taxonomy->getTree('categories');
        $expected = [
            'uncategorized' => 'Uncategorized',
            'announcements' => 'Announcements',
        ];
        $this->assertEqual($tree, $expected);
    }

    public function testTermInVocabulary()
    {
        $this->assertEquals(1, $this->Taxonomy->termInVocabulary(1, 1)); // Uncategorized in Categories
        $this->assertFalse($this->Taxonomy->termInVocabulary(1, 3)); // Uncategorized in non-existing vocabulary
    }
}
