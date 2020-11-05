<?php
namespace Croogo\Users\Test\TestCase\Controller;

use Croogo\Core\TestSuite\CroogoControllerTestCase;

/**
 * UsersController Test
 *
 * @uses \Croogo\Users\Controller\UsersController
 */
class UsersControllerTest extends CroogoControllerTestCase
{

    /**
     * fixtures
     *
     * @var array
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
        $this->UsersController = $this->generate('Users.Users', [
            'methods' => [
                'redirect',
                'onAdminLoginFailure',
            ],
            'components' => [
                'Auth' => ['user', 'identify', 'login'],
                'Session',
                'Security',
            ],
        ]);
        $this->controller->helpers = [
            'Html' => [
                'className' => 'Croogo/Core.Html',
            ],
        ];

        $this->controller->Auth
            ->staticExpects($this->any())
            ->method('identify')
            ->will($this->returnCallback([$this, 'authIdentifyFalse']));
    }

    protected function _setupAuthUser()
    {
        $this->controller->Auth
            ->staticExpects($this->any())
            ->method('user')
            ->will($this->returnCallback([$this, 'authUserCallback']));
    }

    public function authIdentifyFalse()
    {
        return false;
    }

    public function authIdentifyTrue()
    {
        return true;
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->UsersController);
    }

    /**
     * testAdminIndex
     *
     * @return void
     */
    public function testAdminIndex()
    {
        $this->_setupAuthUser();
        $this->testAction('/admin/users/users/index');
        $this->assertNotEmpty($this->vars['displayFields']);
        $this->assertNotEmpty($this->vars['users']);
        $this->assertEquals(3, count($this->vars['users']));
    }

    /**
     * testAdminIndexSearch
     *
     * @return void
     */
    public function testAdminIndexSearch()
    {
        $this->_setupAuthUser();
        $this->testAction('/admin/users/users/index?name=admin');
        $this->assertEquals(1, count($this->vars['users']));
    }

    /**
     * testAddtestAddInvalidPassword
     *
     * @return void
     */
    public function testAddInvalidPassword()
    {
        $this->_setupAuthUser();
        $_SERVER['SERVER_NAME'] = 'croogo.dev';
        $this->UsersController->Session
            ->expects($this->once())
            ->method('setFlash')
            ->with(
                $this->equalTo('The User could not be saved. Please, try again.'),
                $this->equalTo('flash'),
                $this->equalTo(['class' => 'error'])
            );
        $this->testAction('/users/users/add', [
            'data' => [
                'User' => [
                    'username' => 'new_user',
                    'password' => '',
                    'email' => 'new_user@croogo.dev',
                    'name' => 'New User',
                    'website' => '',
                    'role_id' => 3,
                ],
            ],
        ]);
        $errors = print_r($this->UsersController->User->validationErrors, true);
        $this->assertContains('at least 6 characters', $errors);
    }

    /**
     * testAddtestAddOtherErrors
     *
     * @return void
     */
    public function testAddtestAddOtherErrors()
    {
        $this->_setupAuthUser();
        $_SERVER['SERVER_NAME'] = 'croogo.dev';
        $this->UsersController->Session
            ->expects($this->once())
            ->method('setFlash')
            ->with(
                $this->equalTo('The User could not be saved. Please, try again.'),
                $this->equalTo('flash'),
                $this->equalTo(['class' => 'error'])
            );
        $this->testAction('/users/users/add', [
            'data' => [
                'User' => [
                    'username' => 'admin',
                    'password' => 'yvonne',
                    'verify_password' => 'strahovski',
                    'email' => '123456',
                    'name' => 'New User',
                    'website' => '',
                    'role_id' => 3,
                ],
            ],
        ]);
        $errors = print_r($this->UsersController->User->validationErrors, true);
        $this->assertContains('do not match', $errors);
        $this->assertContains('valid email', $errors);
        $this->assertContains('been taken', $errors);
    }

    /**
     * testAdminAdd
     *
     * @return void
     */
    public function testAdminAdd()
    {
        $this->_setupAuthUser();
        $this->expectFlashAndRedirect('The User has been saved');
        $this->testAction('/admin/users/users/add', [
            'data' => [
                'User' => [
                    'username' => 'new_user',
                    'password' => uniqid(),
                    'email' => 'new_user@croogo.dev',
                    'name' => 'New User',
                    'role_id' => 3,
                ],
            ],
        ]);
        $newUser = $this->UsersController->User->findByUsername('new_user');
        $this->assertEqual($newUser['User']['name'], 'New User');
    }

    /**
     * testAdminEdit
     *
     * @return void
     */
    public function testAdminEdit()
    {
        $this->_setupAuthUser();
        $this->expectFlashAndRedirect('The User has been saved');
        $this->testAction('/admin/users/users/edit/1', [
            'data' => [
                'User' => [
                    'id' => 1, // admin
                    'name' => 'Administrator [modified]',
                    'role_id' => 1,
                ],
            ],
        ]);
        $expected = 'Administrator [modified]';
        $this->assertEquals($expected, $this->controller->request->data['User']['name']);
        $result = $this->controller->User->findByUsername('admin');
        $this->assertEquals($expected, $result['User']['name']);
    }

    /**
     * testAdminResetPassword
     *
     * @return void
     */
    public function testAdminResetPassword()
    {
        $this->_setupAuthUser();
        $this->expectFlashAndRedirect('Password has been reset.');
        $this->testAction('/admin/users/users/reset_password/1', [
            'data' => [
                'User' => [
                    'id' => 1,
                    'password' => 'foobar',
                    'verify_password' => 'foobar',
                ],
            ],
        ]);
    }

    /**
     * testAdminResetPasswordValidationErrors
     *
     * @return void
     */
    public function testAdminResetPasswordValidationErrors()
    {
        $this->_setupAuthUser();
        $result = $this->testAction('/admin/users/users/reset_password/1', [
            'data' => [
                'User' => [
                    'id' => 1,
                    'password' => '123',
                    'verify_password' => '123',
                ],
            ],
            'return' => 'view',
        ]);
        $this->assertContains('Passwords must be at least 6 characters long.', $result);
    }

    /**
     * testAdminDelete
     *
     * @return void
     */
    public function testAdminDelete()
    {
        $this->_setupAuthUser();
        $this->expectFlashAndRedirect('User deleted');
        $this->testAction('/admin/users/users/delete/2'); // ID of rchavik
        $hasAny = $this->UsersController->User->hasAny([
            'User.username' => 'rchavik',
        ]);
        $this->assertFalse($hasAny);
    }

    /**
     * testAdminDeleteCurrentUser
     *
     * @return void
     */
    public function testAdminDeleteCurrentUser()
    {
        $this->_setupAuthUser();
        // check that another admin exists
        $hasAny = $this->UsersController->User->hasAny([
            'User.username' => 'rchavik',
            'User.role_id' => 1,
        ]);
        $this->assertTrue($hasAny);

        // delete admin
        $this->expectFlashAndRedirect('User deleted');
        $this->testAction('/admin/users/users/delete/1'); // ID of admin

        $hasAny = $this->UsersController->User->hasAny([
            'User.role_id' => 1,
        ]);
        $this->assertTrue($hasAny);
    }

    /**
     * testResetPasswordWithValidInfo
     *
     * @return void
     */
    public function testResetPasswordWithValidInfo()
    {
        $this->_setupAuthUser();
        $this->testAction(
            sprintf('/users/users/reset/%s/%s', 'yvonne', '92e35177eba73c6524d4561d3047c0c2')
        );
        $this->assertTrue(isset($this->vars['key']));
    }

    /**
     * testResetPasswordWithInvalidInfo
     *
     * @return void
     */
    public function testResetPasswordWithInvalidInfo()
    {
        $this->_setupAuthUser();
        $this->UsersController->Session
            ->expects($this->once())
            ->method('setFlash')
            ->with(
                $this->equalTo('An error occurred.'),
                $this->equalTo('flash'),
                $this->equalTo(['class' => 'error'])
            );
        $this->UsersController
            ->expects($this->once())
            ->method('redirect');
        $this->testAction(
            sprintf('/users/users/reset/%s/%s', 'yvonne', 'invalid')
        );
    }

    /**
     * testResetPasswordUpdatesPassword
     *
     * @return void
     */
    public function testResetPasswordUpdatesPassword()
    {
        $this->_setupAuthUser();
        $this->testAction(
            sprintf('/users/users/reset/%s/%s', 'yvonne', '92e35177eba73c6524d4561d3047c0c2'),
            [
                'data' => [
                    'User' => [
                        'password' => 'newpassword',
                        'verify_password' => 'newpassword',
                    ]
                ]
            ]
        );
        $user = $this->UsersController->User->findByUsername('yvonne');

        $expected = AuthComponent::password('newpassword');
        $this->assertEqual($expected, $user['User']['password'], sprintf("%s to be %s", $user['User']['password'], $expected));
    }

    /**
     * testResetPasswordWithMismatchValues
     *
     * @return void
     */
    public function testResetPasswordWithMismatchValues()
    {
        $this->_setupAuthUser();
        $this->testAction(
            sprintf('/users/users/reset/%s/%s', 'yvonne', '92e35177eba73c6524d4561d3047c0c2'),
            [
                'return' => 'contents',
                'data' => [
                    'User' => [
                        'id' => 3,
                        'password' => 'otherpassword',
                        'verify_password' => 'other password',
                    ]
                ]
            ]
        );
        $this->assertContains('Passwords do not match', $this->contents);
    }

    /**
     * testAdminLoginFailureEvent
     *
     * @return void
     */
    public function testAdminLoginFailureEvent()
    {
        $this->controller->Auth->request = $this->controller->request;
        $this->controller->Auth->response = $this->controller->response;
        $this->controller->Auth->Session = $this->controller->Session;
        $this->controller->expects($this->once())
            ->method('onAdminLoginFailure')
            ->will($this->returnValue(true));
        $this->testAction(
            '/admin/users/users/login',
            [
                'method' => 'POST',
                'return' => 'result',
                'data' => [
                    'User' => [
                        'username' => 'orange',
                        'password' => 'banana',
                        'verify_password' => 'banana',
                    ]
                ]
            ]
        );
    }

    /**
     * Test correct redirection after login in frontend
     *
     * @return void
     */
    public function testRedirectAfterAdminLogin()
    {
        $controller = $this->generate('Users.Users', [
            'methods' => [
                'redirect',
            ],
            'components' => [
                'Auth' => ['login'],
            ],
        ]);
        $controller->Auth
            ->expects($this->any())
            ->method('login')
            ->will($this->returnCallback([$this, 'authIdentifyTrue']));
        $controller->expects($this->once())
            ->method('redirect')
            ->with(Router::url(Configure::read('Croogo.dashboardUrl')));
        $this->testAction(
            '/admin/users/users/login',
            [
                'method' => 'POST',
            ]
        );
    }

    /**
     * Test correct redirection after login in /admin
     *
     * @return void
     */
    public function testRedirectAfterLogin()
    {
        $controller = $this->generate('Users.Users', [
            'methods' => [
                'redirect',
            ],
            'components' => [
                'Auth' => ['login'],
            ],
        ]);
        $controller->Auth
            ->expects($this->any())
            ->method('login')
            ->will($this->returnCallback([$this, 'authIdentifyTrue']));
        $controller->expects($this->once())
            ->method('redirect')
            ->with(Router::url(Configure::read('Site.homeUrl')));
        $this->testAction(
            '/users/users/login',
            [
                'method' => 'POST',
            ]
        );
    }
}
