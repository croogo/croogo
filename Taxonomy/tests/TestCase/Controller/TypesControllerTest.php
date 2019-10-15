<?php
namespace Croogo\Taxonomy\Test\TestCase\Controller;

use Croogo\TestSuite\CroogoControllerTestCase;
use Taxonomy\Controller\TypesController;

class TypesControllerTest extends CroogoControllerTestCase
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

    /**
     * setUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->TypesController = $this->generate('Taxonomy.Types', [
            'methods' => [
                'redirect',
            ],
            'components' => [
                'Auth' => ['user'],
                'Session',
            ],
        ]);
        $this->TypesController->Auth
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
        unset($this->TypesController);
    }

    /**
     * testAdminIndex
     *
     * @return void
     */
    public function testAdminIndex()
    {
        $this->testAction('/admin/types/index');
        $this->assertNotEmpty($this->vars['displayFields']);
        $this->assertNotEmpty($this->vars['types']);
    }

    /**
     * testAdminAdd
     *
     * @return void
     */
    public function testAdminAdd()
    {
        $this->expectFlashAndRedirect('The Type has been saved');
        $this->testAction('admin/taxonomy/types/add', [
            'data' => [
                'Type' => [
                    'title' => 'New Type',
                    'alias' => 'new_type',
                    'description' => 'A new type',
                ],
            ],
        ]);
        $newType = $this->TypesController->Type->findByAlias('new_type');
        $this->assertEqual($newType['Type']['title'], 'New Type');
    }

    /**
     * testAdminEdit
     *
     * @return void
     */
    public function testAdminEdit()
    {
        $this->expectFlashAndRedirect('The Type has been saved');
        $this->testAction('/admin/types/edit/1', [
            'data' => [
                'Type' => [
                    'id' => 1, // page
                    'description' => '[modified]',
                ],
            ],
        ]);
        $page = $this->TypesController->Type->findByAlias('page');
        $this->assertEquals('[modified]', $page['Type']['description']);
    }

    /**
     * testAdminDelete
     *
     * @return void
     */
    public function testAdminDelete()
    {
        $this->expectFlashAndRedirect('Type deleted');
        $this->testAction('/admin/types/delete/1'); // ID of page
        $hasAny = $this->TypesController->Type->hasAny([
            'Type.alias' => 'page',
        ]);
        $this->assertFalse($hasAny);
    }
}
