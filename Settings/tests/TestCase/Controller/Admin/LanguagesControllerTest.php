<?php

namespace Croogo\Settings\Test\TestCase\Controller\Admin;

use Cake\ORM\TableRegistry;
use Croogo\Core\TestSuite\IntegrationTestCase;

class LanguagesControllerTest extends IntegrationTestCase
{
    public $fixtures = [
        'plugin.croogo/blocks.block',
        'plugin.croogo/comments.comment',
        'plugin.croogo/core.settings',
        'plugin.croogo/settings.language',
        'plugin.croogo/menus.menu',
        'plugin.croogo/meta.meta',
        'plugin.croogo/nodes.node',
        'plugin.croogo/users.user',
        'plugin.croogo/users.role',
        'plugin.croogo/taxonomy.type',
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
    }

    public function testAdminIndex()
    {
        $this->get('/admin/settings/Languages/index');

        $this->assertNotEmpty($this->viewVariable('languages'));
    }

    public function testAdminAdd()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/admin/settings/Languages/add', [
            'title' => 'Bengali',
            'alias' => 'ben',
        ]);

        $this->assertRedirect();
        $this->assertFlash('Successfully created language');

        $language = TableRegistry::get('Croogo/Settings.Languages')
            ->findByAlias('ben')
            ->first();
        $this->assertEquals('Bengali', $language->title);
    }

    public function testAdminEdit()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/admin/settings/Languages/edit/1', [
            'id' => 1,
            'title' => 'English [modified]',
            'alias' => 'eng',
        ]);

        $this->assertRedirect();
        $this->assertFlash('Successfully updated language');

        $language = TableRegistry::get('Croogo/Settings.Languages')
            ->findByAlias('eng')
            ->first();
        $this->assertEquals('English [modified]', $language->title);
    }

    public function testAdminDelete()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/admin/settings/Languages/delete/1');

        $this->assertRedirect();
        $this->assertFlash('Successfully deleted language');

        $language = (bool)TableRegistry::get('Croogo/Settings.Languages')
            ->findByAlias('eng')
            ->count();
        $this->assertFalse($language);
    }

/**
 * testAdminMoveUp
 *
 * @return void
 */
    public function testAdminMoveUp()
    {
        $id = $this->_addLanguages();

        $this->post('/admin/settings/Languages/moveUp/' . $id);

        $this->assertRedirect();
        $this->assertFlash('Successfully moved language up');

        $list = TableRegistry::get('Croogo/Settings.Languages')->find('list')->toArray();
        $this->assertEquals([
            1 => 'English',
            3 => 'German',
            2 => 'Bengali',
        ], $list);
    }

    public function testAdminMoveUpWithSteps()
    {
        $id = $this->_addLanguages();

        $this->post('/admin/settings/Languages/moveUp/' . $id . '/2');

        $this->assertRedirect();
        $this->assertFlash('Successfully moved language up');

        $list = TableRegistry::get('Croogo/Settings.Languages')->find('list')->toArray();
        $this->assertEquals([
            3 => 'German',
            2 => 'Bengali',
            1 => 'English',
        ], $list);
    }

    public function testAdminMoveDown()
    {
        $this->_addLanguages();

        $this->post('/admin/settings/Languages/moveUp/1');

        $this->assertRedirect();
        $this->assertFlash('Successfully moved language up');

        $list = TableRegistry::get('Croogo/Settings.Languages')->find('list')->toArray();
        $this->assertEquals([
            2 => 'Bengali',
            3 => 'German',
            1 => 'English',
        ], $list);
    }

    public function testAdminMoveDownWithSteps()
    {
        $this->_addLanguages();

        $this->post('/admin/settings/Languages/moveUp/1/2');

        $this->assertRedirect();
        $this->assertFlash('Successfully moved language up');

        $list = TableRegistry::get('Croogo/Settings.Languages')->find('list')->toArray();
        $this->assertEquals([
            3 => 'German',
            2 => 'Bengali',
            1 => 'English',
        ], $list);
    }

/**
 * testAdminSelect
 *
 * @return void
 */
    public function testAdminSelect()
    {
        $this->markTestIncomplete('Still being ported');

        $this->LanguagesController
            ->expects($this->once())
            ->method('redirect');
        $this->testAction('/admin/languages/select');

        $this->testAction('/admin/languages/select/1/Node');
        $this->assertEqual(1, $this->vars['id']);
        $this->assertEqual('Node', $this->vars['modelAlias']);
        $this->assertEqual('English', $this->vars['languages']['0']['Language']['title']);
        $this->assertEqual('eng', $this->vars['languages']['0']['Language']['alias']);
    }

    /**
     * Helper for adding languages
     *
     * @return int id of last added
     */
    protected function _addLanguages()
    {
        $languages = TableRegistry::get('Croogo/Settings.Languages');
        $languages->save($languages->newEntity([
            'title' => 'Bengali',
            'alias' => 'ben',
        ]));

        $german = $languages->newEntity([
            'title' => 'German',
            'alias' => 'deu',
        ]);

        $languages->save($german);
        return $german->id;
    }
}
