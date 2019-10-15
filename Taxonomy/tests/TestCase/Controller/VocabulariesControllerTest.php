<?php
namespace Croogo\Taxonomy\Test\TestCase\Controller;

use Croogo\TestSuite\CroogoControllerTestCase;
use Taxonomy\Controller\VocabulariesController;

/**
 * VocabulariesController Test
 */
class VocabulariesControllerTest extends CroogoControllerTestCase
{

    /**
     * fixtures
     */
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

    /**
     * setUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        App::build([
            'View' => [Plugin::path('Taxonomy') . 'View' . DS]
        ], App::APPEND);
        $this->VocabulariesController = $this->generate('Taxonomy.Vocabularies', [
            'methods' => [
                'redirect',
            ],
            'components' => [
                'Auth' => ['user'],
                'Session',
            ],
        ]);
        $this->VocabulariesController->Auth
            ->staticExpects($this->any())
            ->method('user')
            ->will($this->returnCallback([$this, 'authUserCallback']));
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->VocabulariesController);
    }

    /**
     * testAdminIndex
     *
     * @return void
     */
    public function testAdminIndex()
    {
        $this->testAction('/admin/taxonomy/vocabularies/index');
        $this->assertNotEmpty($this->vars['vocabularies']);
    }

    /**
     * testAdminAdd
     *
     * @return void
     */
    public function testAdminAdd()
    {
        $this->expectFlashAndRedirect('The Vocabulary has been saved');
        $this->testAction('admin/taxonomy/vocabularies/add', [
            'data' => [
                'Vocabulary' => [
                    'title' => 'New Vocabulary',
                    'alias' => 'new_vocabulary',
                ],
            ],
        ]);
        $newVocabulary = $this->VocabulariesController->Vocabulary->findByAlias('new_vocabulary');
        $this->assertEqual($newVocabulary['Vocabulary']['title'], 'New Vocabulary');
    }

    /**
     * testAdminEdit
     *
     * @return void
     */
    public function testAdminEdit()
    {
        $this->expectFlashAndRedirect('The Vocabulary has been saved');
        $this->testAction('/admin/taxonomy/vocabularies/edit/1', [
            'data' => [
                'Vocabulary' => [
                    'id' => 1, // categories
                    'title' => 'Categories [modified]',
                ],
            ],
        ]);
        $categories = $this->VocabulariesController->Vocabulary->findByAlias('categories');
        $this->assertEquals('Categories [modified]', $categories['Vocabulary']['title']);
    }

    /**
     * testAdminDelete
     *
     * @return void
     */
    public function testAdminDelete()
    {
        $this->expectFlashAndRedirect('Vocabulary deleted');
        $this->testAction('admin/taxonomy/vocabularies/delete/1'); // ID of categories
        $hasAny = $this->VocabulariesController->Vocabulary->hasAny([
            'Vocabulary.alias' => 'categories',
        ]);
        $this->assertFalse($hasAny);
    }

    /**
     * testAdminMoveup
     *
     * @return void
     */
    public function testAdminMoveup()
    {
        $this->expectFlashAndRedirect('Moved up successfully');
        $this->testAction('admin/taxonomy/vocabularies/moveup/2'); // ID of tags
        $vocabularies = $this->VocabulariesController->Vocabulary->find('list', [
            'fields' => [
                'id',
                'alias',
            ],
            'order' => 'Vocabulary.weight ASC',
        ]);
        $expected = [
            '2' => 'tags',
            '1' => 'categories',
        ];
        $this->assertEqual($vocabularies, $expected);
    }

    /**
     * testAdminMovedown
     *
     * @return void
     */
    public function testAdminMovedown()
    {
        $this->expectFlashAndRedirect('Moved down successfully');
        $this->testAction('admin/taxonomy/vocabularies/movedown/1'); // ID of categories
        $vocabularies = $this->VocabulariesController->Vocabulary->find('list', [
            'fields' => [
                'id',
                'alias',
            ],
            'order' => 'Vocabulary.weight ASC',
        ]);
        $expected = [
            '2' => 'tags',
            '1' => 'categories',
        ];
        $this->assertEqual($vocabularies, $expected);
    }
}
