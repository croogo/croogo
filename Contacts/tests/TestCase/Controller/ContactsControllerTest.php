<?php
namespace Croogo\Contacts\Test\TestCase\Controller;

use Contacts\Controller\ContactsController;
use Croogo\TestSuite\CroogoControllerTestCase;
use Croogo\TestSuite\CroogoTestFixture;

class ContactsControllerTest extends CroogoControllerTestCase
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
        $this->ContactsController = $this->generate('Contacts.Contacts', [
            'methods' => [
                'redirect',
                '_send_email',
            ],
            'components' => [
                'Auth' => ['user'],
                'Session',
            ],
        ]);
        $this->controller->plugin = 'Contacts';
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
        unset($this->ContactsController);
    }

    /**
     * testAdminIndex
     *
     * @return void
     */
    public function testAdminIndex()
    {
        $this->testAction('/admin/contacts/contacts/index');
        $this->assertNotEmpty($this->vars['contacts']);
    }

    /**
     * testAdminAdd
     *
     * @return void
     */
    public function testAdminAdd()
    {
        $this->expectFlashAndRedirect('The Contact has been saved');
        $this->testAction('/admin/contacts/contacts/add', [
            'data' => [
                'Contact' => [
                    'title' => 'New contact',
                    'alias' => 'new_contact',
                ],
            ],
        ]);
        $newContact = $this->ContactsController->Contact->findByAlias('new_contact');
        $this->assertEqual($newContact['Contact']['title'], 'New contact');
    }

    /**
     * testAdminEdit
     *
     * @return void
     */
    public function testAdminEdit()
    {
        $this->expectFlashAndRedirect('The Contact has been saved');
        $this->testAction('/admin/contacts/contacts/edit/1', [
            'data' => [
                'Contact' => [
                    'id' => 1,
                    'title' => 'Contact [modified]',
                ],
            ],
        ]);
        $result = $this->controller->Contact->findByAlias('contact');
        $this->assertEquals('Contact [modified]', $result['Contact']['title']);
    }

    /**
     * testAdminDelete
     *
     * @return void
     */
    public function testAdminDelete()
    {
        $this->expectFlashAndRedirect('Contact deleted');
        $this->testAction('admin/contacts/contacts/delete/1');
        $hasAny = $this->ContactsController->Contact->hasAny([
            'Contact.alias' => 'contact',
        ]);
        $this->assertFalse($hasAny);
    }

    /**
     * testView
     */
    public function testView()
    {
        $Contacts = $this->generate('Contacts', [
            'methods' => [
                '_spam_protection',
                '_captcha',
                '_send_email'
            ],
        ]);
        $Contacts->plugin = 'Contacts';
        $Contacts->expects($this->once())
            ->method('_spam_protection')
            ->will($this->returnValue(true));
        $Contacts->expects($this->once())
            ->method('_captcha')
            ->will($this->returnValue(true));
        $Contacts->expects($this->once())
            ->method('_send_email')
            ->will($this->returnValue(true));
        $this->controller->request->params['action'] = 'view';
        $this->controller->request->params['url']['url'] = 'contacts/contacts/view/contact';
        $this->controller->request->data = [
            'Message' => [
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'title' => 'Hello',
                'body' => 'text here',
            ],
        ];
        $this->controller->startupProcess();
        $this->controller->view('contact');
        $this->assertEqual($this->controller->viewVars['continue'], true);

        $this->controller->testView = true;
        $output = $this->controller->render('view');
        $this->assertFalse(strpos($output, '<pre class="cake-debug">'));
    }
}
