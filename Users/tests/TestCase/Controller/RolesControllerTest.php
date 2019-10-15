<?php
namespace Croogo\Users\Test\TestCase\Controller;

use Croogo\Core\TestSuite\CroogoControllerTestCase;

class RolesControllerTest extends CroogoControllerTestCase
{

    public $fixtures = [
        'plugin.Croogo/Users.Role',
        'plugin.Croogo/Users.User',
        'plugin.Croogo/Users.Aco',
        'plugin.Croogo/Users.Aro',
        'plugin.Croogo/Users.ArosAco',
//      'plugin.Croogo/Blocks.Block',
//      'plugin.Croogo/Comments.Comment',
//      'plugin.Croogo/Contacts.Contact',
//      'plugin.Croogo/Translate.I18n',
        'plugin.Croogo/Settings.Language',
//      'plugin.Croogo/Menus.Link',
//      'plugin.Croogo/Menus.Menu',
//      'plugin.Croogo/Contacts.Message',
//      'plugin.Croogo/Meta.Meta',
        'plugin.Croogo/Nodes.Node',
//      'plugin.Croogo/Taxonomy.ModelTaxonomy',
//      'plugin.Croogo/Blocks.Region',
        'plugin.Croogo/Settings.Setting',
//      'plugin.Croogo/Taxonomy.Taxonomy',
//      'plugin.Croogo/Taxonomy.Term',
//      'plugin.Croogo/Taxonomy.Type',
//      'plugin.Croogo/Taxonomy.TypesVocabulary',
//      'plugin.Croogo/Taxonomy.Vocabulary',
    ];

    /**
     * setUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->RolesController = $this->generate('Users.Roles', [
            'methods' => [
                'redirect',
            ],
            'components' => [
                'Auth' => ['user'],
                'Session',
                'Menus.Menus',
            ],
        ]);
        $this->controller->Auth
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
        unset($this->RolesController);
    }

    /**
     * testAdminIndex
     *
     * @return void
     */
    public function testAdminIndex()
    {
        $this->testAction('/admin/users/roles/index');
        $this->assertNotEmpty($this->vars['displayFields']);
        $this->assertNotEmpty($this->vars['roles']);
    }

    /**
     * testAdminAdd
     *
     * @return void
     */
    public function testAdminAdd()
    {
        $this->expectFlashAndRedirect('The Role has been saved');
        $this->testAction('admin/users/roles/add', [
            'data' => [
                'Role' => [
                    'title' => 'new_role',
                    'alias' => 'new_role',
                ],
            ],
        ]);
        $newRole = $this->RolesController->Role->findByAlias('new_role');
        $this->assertEqual($newRole['Role']['title'], 'new_role');
    }

    /**
     * testAdminIndex
     *
     * @return void
     */
    public function testAdminEdit()
    {
        $this->expectFlashAndRedirect('The Role has been saved');
        $this->testAction('/admin/users/roles/edit/1', [
            'data' => [
                'Role' => [
                    'id' => 2, // Registered
                    'title' => 'Registered [modified]',
                ],
            ],
        ]);
        $registered = $this->controller->Role->findByAlias('registered');
        $this->assertEquals('Registered [modified]', $registered['Role']['title']);
    }

    /**
     * testAdminDelete
     *
     * @return void
     */
    public function testAdminDelete()
    {
        $this->expectFlashAndRedirect('Role deleted');
        $this->testAction('/admin/users/roles/delete/1'); // ID of Admin
        $hasAny = $this->RolesController->Role->hasAny([
            'Role.alias' => 'admin',
        ]);
        $this->assertFalse($hasAny);
    }
}
