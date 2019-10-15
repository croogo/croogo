<?php
namespace Croogo\Taxonomy\Test\TestCase\Controller;

use Croogo\TestSuite\CroogoControllerTestCase;
use Taxonomy\Controller\TermsController;

class TermsControllerTest extends CroogoControllerTestCase
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
        $this->TermsController = $this->generate('Taxonomy.Terms', [
            'methods' => [
                'redirect',
            ],
            'components' => [
                'Auth' => ['user'],
                'Session',
                'Menus.Menus',
            ],
        ]);
        $this->TermsController->Auth
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
        unset($this->TermsController);
    }

    /**
     * testAdminIndex
     *
     * @return void
     */
    public function testAdminIndex()
    {
        $this->testAction('/admin/taxonomy/terms/index/1');
        $this->assertNotEmpty($this->vars['terms']);
        $expected = [
            '1' => 'Uncategorized',
            '2' => 'Announcements',
        ];
        $termsTree = Hash::combine($this->vars['terms'], '{n}.Term.id', '{n}.Term.title');
        $this->assertEquals($expected, $termsTree);
    }

    /**
     * testAdminAdd
     *
     * @return void
     */
    public function testAdminAdd()
    {
        $this->expectFlashAndRedirect('Term saved successfuly.');
        $this->testAction('admin/taxonomy/terms/add/1', [
            'data' => [
                'Taxonomy' => [
                    'parent_id' => null,
                ],
                'Term' => [
                    'title' => 'New Category',
                    'slug' => 'new-category',
                    'description' => 'category description here',
                ],
            ],
        ]);
        $termsTree = $this->TermsController->Term->Taxonomy->getTree('categories');
        $termsTreeSlugs = array_keys($termsTree);
        $expected = [
            'uncategorized',
            'announcements',
            'new-category',
        ];
        $this->assertEqual($termsTreeSlugs, $expected);
    }

    /**
     * testAdminAddWithParent
     *
     * @return void
     */
    public function testAdminAddWithParent()
    {
        $this->expectFlashAndRedirect('Term saved successfuly.');
        $this->testAction('admin/taxonomy/terms/add/1', [
            'data' => [
                'Taxonomy' => [
                    'parent_id' => 1, // Uncategorized
                ],
                'Term' => [
                    'title' => 'New Category',
                    'slug' => 'new-category',
                    'description' => 'category description here',
                ],
            ],
        ]);
        $termsTree = $this->TermsController->Term->Taxonomy->getTree('categories');
        $termsTreeTitles = array_values($termsTree);
        $expected = [
            'Uncategorized',
            '_New Category',
            'Announcements',
        ];
        $this->assertEqual($termsTreeTitles, $expected);
    }

    /**
     * testAdminEdit
     *
     * @return void
     */
    public function testAdminEdit()
    {
        $this->expectFlashAndRedirect('Term saved successfuly.');
        // ID of Uncategorized and Categories
        $this->testAction('/admin/taxonomy/terms/edit/1/1', [
            'data' => [
                'Taxonomy' => [
                    'id' => 1,
                    'parent_id' => null,
                ],
                'Term' => [
                    'id' => 1,
                    'title' => 'New Category',
                    'slug' => 'new-category',
                    'description' => 'category description here',
                ],
            ],
        ]);
        $termsTree = $this->TermsController->Term->Taxonomy->getTree('categories');
        $expected = [
            'new-category' => 'New Category',
            'announcements' => 'Announcements',
        ];
        $this->assertEquals($expected, $termsTree);
    }

    /**
     * testAdminDelete
     *
     * @return void
     */
    public function testAdminDelete()
    {
        $ModelTaxonomy = ClassRegistry::init('Taxonomy.ModelTaxonomy');
        $options = ['conditions' => [
            'taxonomy_id' => 1,
        ]];
        $count = $ModelTaxonomy->find('count', $options);
        $this->assertEquals(1, $count);

        $this->expectFlashAndRedirect('Term deleted');
        $this->testAction('admin/taxonomy/terms/delete/1/1'); // ID of Uncategorized and Categories
        $termsTree = $this->TermsController->Term->Taxonomy->getTree('categories');

        $count = $ModelTaxonomy->find('count', $options);
        $this->assertEquals(0, $count);

        $expected = [
            'announcements' => 'Announcements',
        ];
        $this->assertEqual($termsTree, $expected);
    }

    /**
     * testAdminMoveup
     *
     * @return void
     */
    public function testAdminMoveup()
    {
        $this->expectFlashAndRedirect('Moved up successfully');
        $this->testAction('admin/taxonomy/terms/moveup/2/1'); // ID of Announcements and Categories
        $termsTree = $this->TermsController->Term->Taxonomy->getTree('categories');
        $expected = [
            'announcements' => 'Announcements',
            'uncategorized' => 'Uncategorized',
        ];
        $this->assertEqual($termsTree, $expected);
    }

    /**
     * testAdminMovedown
     *
     * @return void
     */
    public function testAdminMovedown()
    {
        $this->expectFlashAndRedirect('Moved down successfully');
        $this->testAction('admin/taxonomy/terms/movedown/1/1'); // ID of Uncategorized and Categories
        $termsTree = $this->TermsController->Term->Taxonomy->getTree('categories');
        $expected = [
            'announcements' => 'Announcements',
            'uncategorized' => 'Uncategorized',
        ];
        $this->assertEqual($termsTree, $expected);
    }
}
