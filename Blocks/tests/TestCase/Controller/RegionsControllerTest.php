<?php

namespace Croogo\Blocks\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Croogo\Core\TestSuite\IntegrationTestCase;

/**
 * @property \Croogo\Blocks\Model\Table\RegionsTable Regions
 */
class RegionsControllerTest extends IntegrationTestCase
{
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

    public function setUp()
    {
        parent::setUp();

        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 1,
                    'username' => 'admin',
                    'role_id' => 1,
                    'name' => 'Administrator',
                    'email' => 'you@your-site.com',
                    'website' => '/about'
                ]
            ]
        ]);

        $this->Regions = TableRegistry::get('Croogo/Blocks.Regions');
    }

    public function testAdminIndex()
    {
        $this->get('/admin/blocks/regions/index');

        $this->assertNotEmpty($this->viewVariable('displayFields'));
        $this->assertNotEmpty($this->viewVariable('regions'));
    }

    public function testAdminAdd()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/admin/blocks/regions/add', [
            'title' => 'new_region',
            'alias' => 'new_region',
            'description' => 'A new region',
        ]);

        $this->assertRedirect();
        $this->assertFlash('Successfully created region');

        $region = $this->Regions
            ->findByAlias('new_region')
            ->first();
        $this->assertEquals('new_region', $region->title);
    }

    public function testAdminEdit()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/admin/blocks/regions/edit/4', [
            'id' => 4, // right
            'title' => 'right_modified',
        ]);

        $this->assertRedirect();
        $this->assertFlash('Successfully updated region');

        $region = $this->Regions
            ->findByAlias('right')
            ->first();
        $this->assertEquals('right_modified', $region->title);
    }

    public function testAdminDelete()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/admin/blocks/regions/delete/4');

        $this->assertRedirect();
        $this->assertFlash('Successfully deleted region');

        $region = (bool)$this->Regions
            ->findByAlias('right')
            ->count();
        $this->assertFalse($region);
    }
}
