<?php

namespace Croogo\Translate\Test\TestCase\Controller;

use Croogo\TestSuite\CroogoControllerTestCase;
use Translate\Event\TranslateEventHandler;
use Translate\Lib\Translations;

class TranslateControllerTest extends CroogoControllerTestCase
{

    public $fixtures = [
        'plugin.Croogo/Users.Aro',
        'plugin.Croogo/Users.Aco',
        'plugin.Croogo/Users.ArosAco',
        'plugin.Croogo/Comments.Comment',
        'plugin.Croogo/Menus.Menu',
        'plugin.Croogo/Meta.Meta',
        'plugin.Croogo/Nodes.Node',
        'plugin.Croogo/Settings.Language',
        'plugin.Croogo/Settings.Setting',
        'plugin.Croogo/Taxonomy.Taxonomy',
        'plugin.Croogo/Taxonomy.ModelTaxonomy',
        'plugin.Croogo/Taxonomy.Type',
        'plugin.Croogo/Taxonomy.TypesVocabulary',
        'plugin.Croogo/Taxonomy.Vocabulary',
        'plugin.Croogo/Translate.I18n',
        'plugin.Croogo/Users.User',
        'plugin.Croogo/Users.Role',
    ];

    /**
     * setUp
     */
    public function setUp()
    {
        parent::setUp();
        if (!Plugin::loaded('Translate')) {
            Plugin::load('Translate');
        }
        Translations::translateModels();
        $this->TranslateController = $this->generate('Translate.Translate', [
            'methods' => [
                'redirect',
            ],
            'components' => [
                'Auth' => ['user'],
                'Session',
                ]
            ]);
        $this->TranslateController->Auth
            ->staticExpects($this->any())
            ->method('user')
            ->will($this->returnCallback([$this, 'authUserCallback']));
        $this->TranslateController->Security->Session = $this->getMock('Session');
    }

    /**
     * test admin_index action
     */
    public function testAdminIndex()
    {
        $this->testAction('/admin/translate/translate/index/2/Node');
        $this->assertEquals(2, $this->vars['record']['Node']['id']);
        $this->assertEquals('Node', $this->vars['modelAlias']);
    }

    /**
     * test admin_edit action with invalid language
     */
    public function testAdminEditWithBogusLanguage()
    {
        $this->expectFlashAndRedirect('Invalid Language', null, [
            'params' => [
                'class' => 'error',
            ],
        ]);
        $this->testAction('/admin/translate/translate/edit/2/Node/locale:lol');
    }

    /**
     * test admin_edit action
     */
    public function testAdminEdit()
    {
        $this->testAction('/admin/translate/translate/edit/2/Node/locale:eng');
        $this->assertEquals(2, $this->vars['id']);
        $this->assertEquals('Node', $this->vars['modelAlias']);
        $this->assertEquals(1, $this->vars['language']['Language']['id']);
        $this->assertEquals(2, $this->controller->request->data['Node']['id']);
    }

    /**
     * test saving translation with admin_edit action
     */
    public function testSaveWithAdminEdit()
    {
        $this->expectFlashAndRedirect('Record has been translated');
        $this->testAction('/admin/translate/translate/edit/2/Node/locale:eng', [
            'data' => [
                'Node' => [
                    'id' => 2,
                    'title' => 'Hello world [in English locale]',
                    'slug' => 'hello-world-in-english-locale',
                    'type' => 'blog',
                ],
                'Role' => [
                    'Role' => [],
                ],
            ],
        ]);
    }
}
