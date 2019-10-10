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
        'plugin.Croogo/Users.Role',
        'plugin.Croogo/Users.User',
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
        'plugin.Croogo/Core.Settings',
        'plugin.Croogo/Taxonomy.Taxonomy',
        'plugin.Croogo/Taxonomy.Term',
        'plugin.Croogo/Taxonomy.Type',
        'plugin.Croogo/Taxonomy.TypesVocabulary',
        'plugin.Croogo/Taxonomy.Vocabulary',
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
