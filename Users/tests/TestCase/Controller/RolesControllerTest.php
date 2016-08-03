<?php
namespace Croogo\Users\Test\TestCase\Controller;

use Croogo\Core\TestSuite\CroogoControllerTestCase;

class RolesControllerTest extends CroogoControllerTestCase
{

    public $fixtures = [
        'plugin.croogo/users.role',
        'plugin.croogo/users.user',
        'plugin.croogo/users.aco',
        'plugin.croogo/users.aro',
        'plugin.croogo/users.aros_aco',
//		'plugin.blocks.block',
//		'plugin.comments.comment',
//		'plugin.contacts.contact',
//		'plugin.translate.i18n',
        'plugin.croogo/settings.language',
//		'plugin.menus.link',
//		'plugin.menus.menu',
//		'plugin.contacts.message',
//		'plugin.meta.meta',
        'plugin.croogo/nodes.node',
//		'plugin.taxonomy.model_taxonomy',
//		'plugin.blocks.region',

        'plugin.croogo/settings.setting',
//		'plugin.taxonomy.taxonomy',
//		'plugin.taxonomy.term',
//		'plugin.taxonomy.type',
//		'plugin.taxonomy.types_vocabulary',
//		'plugin.taxonomy.vocabulary',
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
