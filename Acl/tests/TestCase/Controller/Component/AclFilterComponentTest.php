<?php

namespace Croogo\Acl\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Croogo\TestSuite\CroogoTestCase;

class AclFilterTestController extends Controller
{

    public $components = [
        'Auth',
        'Acl',
        'Flash',
        'Croogo/Acl.AclFilter',
    ];
}

class AclFilterComponentTest extends CroogoTestCase
{

    public $fixtures = [
        'plugin.users.aro',
        'plugin.users.aco',
        'plugin.users.aros_aco',
        'plugin.users.user',
        'plugin.users.role',
        'plugin.settings.setting',
    ];

    public function testAllowedActions()
    {
        $request = new Request('/users/view/yvonne');
        $request->addParams([
            'controller' => 'users',
            'action' => 'view',
        ]);
        $response = $this->getMock('Response');
        $this->Controller = new AclFilterTestController($request, $response);
        $this->Controller->name = 'Users';
        $this->Controller->constructClasses();
        $this->Controller->startupProcess();
        $this->Controller->AclFilter->auth();
        $result = $this->Controller->Auth->allowedActions;
        $this->assertTrue(in_array('view', $result));
    }

    public function testPrefixedAllowedActions()
    {
        $request = new Request('/admin/users/view/3');
        $request->addParams([
            'admin' => true,
            'controller' => 'users',
            'action' => 'admin_add',
            3,
        ]);
        $response = $this->getMock('Request');
        $this->Controller = new AclFilterTestController($request, $response);
        $this->Controller->constructClasses();
        $user = [
            'id' => 3,
            'role_id' => 3,
            'username' => 'yvonne',
            ];
        $this->Controller->Session->write('Auth.User', $user);

        $aro = ['Role' => ['id' => 3]];
        $aco = 'controllers/Users/admin_add';

        // Role.3 has no access to Users/admin_add yet
        $allowed = $this->Controller->Acl->check($aro, $aco);
        $this->assertEquals(false, $allowed);

        // grant access to /admin/users/view to Role.3
        $this->Controller->Acl->allow($aro, $aco);

        // new permission active
        $allowed = $this->Controller->Acl->check($aro, $aco);
        $this->assertEquals(true, $allowed);
    }

    public function testLoginActionOverrides()
    {
        $this->Controller = new AclFilterTestController(
            $this->getMock('Request'),
            $this->getMock('Response')
        );
        $this->Controller->constructClasses();
        $this->Controller->startupProcess();
        $expected = [
            'plugin' => null,
            'controller' => 'users',
            'action' => 'login',
        ];
        $this->assertEquals($expected, $this->Controller->Auth->loginAction);

        // X vs X
        Configure::write('Acl.Auth.loginAction', [
            'plugin' => 'example',
            'controller' => 'example',
            'action' => 'index',
        ]);
        unset($this->Controller->request->params['admin']);
        $this->Controller->AclFilter->configureLoginActions();
        $expected = [
            'plugin' => 'example',
            'controller' => 'example',
            'action' => 'index',
        ];
        $this->assertEquals($expected, $this->Controller->Auth->loginAction);

        // X vs 0
        Configure::write('Acl.Auth.loginAction', [
            'plugin' => 'example',
            'controller' => 'example',
            'action' => 'index',
        ]);
        $this->Controller->request->params['admin'] = false;
        $this->Controller->AclFilter->configureLoginActions();
        $expected = [
            'plugin' => 'example',
            'controller' => 'example',
            'action' => 'index',
        ];
        $this->assertEquals($expected, $this->Controller->Auth->loginAction);

        // X vs 1
        Configure::write('Acl.Auth.loginAction', [
            'plugin' => 'example',
            'controller' => 'example',
            'action' => 'index',
        ]);
        $this->Controller->request->params['admin'] = true;
        $this->Controller->AclFilter->configureLoginActions();
        $expected = [
            'plugin' => 'users',
            'controller' => 'users',
            'action' => 'login',
        ];
        $this->assertEquals($expected, $this->Controller->Auth->loginAction);

        // 0 vs X
        Configure::write('Acl.Auth.loginAction', [
            'admin' => false,
            'plugin' => 'example',
            'controller' => 'example',
            'action' => 'index',
        ]);
        unset($this->Controller->request->params['admin']);
        $this->Controller->AclFilter->configureLoginActions();
        $expected = [
            'admin' => false,
            'plugin' => 'example',
            'controller' => 'example',
            'action' => 'index',
        ];
        $this->assertEquals($expected, $this->Controller->Auth->loginAction);

        // 0 VS 0
        Configure::write('Acl.Auth.loginAction', [
            'admin' => false,
            'plugin' => 'example',
            'controller' => 'example',
            'action' => 'index',
        ]);
        $this->Controller->request->params['admin'] = false;
        $this->Controller->AclFilter->configureLoginActions();
        $expected = [
            'admin' => false,
            'plugin' => 'example',
            'controller' => 'example',
            'action' => 'index',
        ];
        $this->assertEquals($expected, $this->Controller->Auth->loginAction);

        // 0 VS 1
        Configure::write('Acl.Auth.loginAction', [
            'admin' => false,
            'plugin' => 'example',
            'controller' => 'example',
            'action' => 'index',
        ]);
        $this->Controller->request->params['admin'] = true;
        $this->Controller->AclFilter->configureLoginActions();
        $expected = [
            'plugin' => 'users',
            'controller' => 'users',
            'action' => 'login',
        ];
        $this->assertEquals($expected, $this->Controller->Auth->loginAction);

        // 1 VS x
        Configure::write('Acl.Auth.loginAction', [
            'admin' => true,
            'plugin' => 'example',
            'controller' => 'example',
            'action' => 'index',
        ]);
        unset($this->Controller->request->params['admin']);
        $this->Controller->AclFilter->configureLoginActions();
        $expected = [
            'plugin' => 'users',
            'controller' => 'users',
            'action' => 'login',
        ];
        $this->assertEquals($expected, $this->Controller->Auth->loginAction);

        // 1 VS 0
        Configure::write('Acl.Auth.loginAction', [
            'admin' => true,
            'plugin' => 'example',
            'controller' => 'example',
            'action' => 'index',
        ]);
        $this->Controller->request->params['admin'] = false;
        $this->Controller->AclFilter->configureLoginActions();
        $expected = [
            'plugin' => 'users',
            'controller' => 'users',
            'action' => 'login',
        ];
        $this->assertEquals($expected, $this->Controller->Auth->loginAction);

        // 1 VS 1
        Configure::write('Acl.Auth.loginAction', [
            'admin' => true,
            'plugin' => 'example',
            'controller' => 'example',
            'action' => 'index',
        ]);
        $this->Controller->request->params['admin'] = true;
        $this->Controller->AclFilter->configureLoginActions();
        $expected = [
            'admin' => true,
            'plugin' => 'example',
            'controller' => 'example',
            'action' => 'index',
        ];
        $this->assertEquals($expected, $this->Controller->Auth->loginAction);

        // string values
        Configure::write('Acl.Auth.loginAction', '/');
        $this->Controller->request->params['admin'] = true;
        $this->Controller->AclFilter->configureLoginActions();
        $expected = '/';
        $this->assertEquals($expected, $this->Controller->Auth->loginAction);

        Configure::write('Acl.Auth.loginAction', '/');
        $this->Controller->request->params['admin'] = false;
        $this->Controller->AclFilter->configureLoginActions();
        $expected = '/';
        $this->assertEquals($expected, $this->Controller->Auth->loginAction);

        Configure::write('Acl.Auth.loginAction', '/');
        unset($this->Controller->request->params['admin']);
        $this->Controller->AclFilter->configureLoginActions();
        $expected = '/';
        $this->assertEquals($expected, $this->Controller->Auth->loginAction);

        unset($this->Controller->AclFilter);
        unset($this->Controller);
    }
}
